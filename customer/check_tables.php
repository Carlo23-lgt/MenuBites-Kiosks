<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "carlo_db";

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . mysqli_connect_error()]));
}

// Arrays to track tables status
$unavailable_tables = []; // Tables marked as unavailable by admin
$occupied_tables = []; // Tables occupied by active orders
$all_tables = []; // Information for all tables

// First, check if the tables management table exists
$check_tables_table = $conn->query("SHOW TABLES LIKE 'tables'");
if ($check_tables_table->num_rows > 0) {
    // Get all tables status from the tables table
    $sql = "SELECT table_number, status FROM tables ORDER BY table_number ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $table_number = (int)$row["table_number"];
            $status = $row["status"];
            
            // Add to all tables list
            $all_tables[$table_number] = [
                "number" => $table_number,
                "status" => $status
            ];
            
            // Add to unavailable tables if marked unavailable
            if ($status === 'unavailable') {
                $unavailable_tables[] = $table_number;
            }
        }
    }
} else {
    // If tables table doesn't exist, initialize 10 default tables
    for ($i = 1; $i <= 10; $i++) {
        $all_tables[$i] = [
            "number" => $i,
            "status" => "available"
        ];
    }
}

// Next, check occupied tables from active orders
$check_orders_table = $conn->query("SHOW TABLES LIKE 'orders'");
if ($check_orders_table->num_rows > 0) {
    // Check if table_number column exists
    $check_column = $conn->query("SHOW COLUMNS FROM orders LIKE 'table_number'");
    if ($check_column->num_rows > 0) {
        // Query to get all occupied tables from active orders
        $sql = "SELECT DISTINCT table_number FROM orders WHERE order_type = 'Dine-In' AND status = 'active'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if (!empty($row["table_number"])) {
                    $table_number = (int)$row["table_number"];
                    $occupied_tables[] = $table_number;
                    
                    // Update the status in all_tables to occupied
                    if (isset($all_tables[$table_number])) {
                        $all_tables[$table_number]["status"] = "occupied";
                    }
                }
            }
        }
    }
}

// Combine both lists - tables that are either occupied or unavailable
$all_unavailable_tables = array_unique(array_merge($occupied_tables, $unavailable_tables));

// Prepare the final response
$tableStatuses = [];
foreach ($all_tables as $table) {
    $tableStatuses[] = [
        "number" => $table["number"],
        "status" => $table["status"],
        "is_available" => ($table["status"] === "available")
    ];
}

// Return JSON response with all tables information
echo json_encode([
    "success" => true,
    "occupied_tables" => $all_unavailable_tables, // For backward compatibility
    "table_statuses" => $tableStatuses // New detailed information
]);

$conn->close();
?> 