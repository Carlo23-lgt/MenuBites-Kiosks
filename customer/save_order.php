<?php

$servername = "localhost"; // Change if needed
$username = "root"; // Your DB username
$password = ""; // Your DB password
$database = "carlo_db"; // Your database name

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . mysqli_connect_error()]));
}

// Check if table exists, create it if not
$check_table = $conn->query("SHOW TABLES LIKE 'orders'");
if ($check_table->num_rows == 0) {
    // Create the table
    $create_table = "CREATE TABLE orders (
        id INT(11) NOT NULL AUTO_INCREMENT,
        order_type VARCHAR(50) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        order_details TEXT NOT NULL,
        table_number VARCHAR(10) NULL,
        status VARCHAR(20) DEFAULT 'active',
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )";
    
    if (!$conn->query($create_table)) {
        die(json_encode(["success" => false, "error" => "Table creation failed: " . $conn->error]));
    }
}

// Check if the tables management table exists, create it if not
$check_tables_table = $conn->query("SHOW TABLES LIKE 'tables'");
if ($check_tables_table->num_rows == 0) {
    // Create the tables table
    $create_tables_table = "CREATE TABLE tables (
        id INT(11) NOT NULL AUTO_INCREMENT,
        table_number INT(11) NOT NULL,
        status ENUM('available', 'unavailable', 'occupied') NOT NULL DEFAULT 'available',
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY (table_number)
    )";
    
    if (!$conn->query($create_tables_table)) {
        die(json_encode(["success" => false, "error" => "Tables table creation failed: " . $conn->error]));
    }
    
    // Initialize with 10 tables
    for ($i = 1; $i <= 10; $i++) {
        $conn->query("INSERT INTO tables (table_number, status) VALUES ($i, 'available')");
    }
}

// Check if table_number column exists, add it if not
$check_table_column = $conn->query("SHOW COLUMNS FROM orders LIKE 'table_number'");
if ($check_table_column->num_rows == 0) {
    if (!$conn->query("ALTER TABLE orders ADD COLUMN table_number VARCHAR(10) NULL")) {
        die(json_encode(["success" => false, "error" => "Could not add table_number column: " . $conn->error]));
    }
}

// Check if status column exists, add it if not
$check_status_column = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
if ($check_status_column->num_rows == 0) {
    if (!$conn->query("ALTER TABLE orders ADD COLUMN status VARCHAR(20) DEFAULT 'active'")) {
        die(json_encode(["success" => false, "error" => "Could not add status column: " . $conn->error]));
    }
}

// Handle the POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the request
    $order_type = isset($_POST["order_type"]) ? $_POST["order_type"] : "";
    $total_amount = isset($_POST["total_amount"]) ? floatval($_POST["total_amount"]) : 0;
    $table_number = isset($_POST["table_number"]) ? intval($_POST["table_number"]) : null;
    
    // Get raw order details and format them properly
    $raw_order_details = isset($_POST["order_details"]) ? urldecode($_POST["order_details"]) : "";
    
    // Get cart items in JSON format for better processing
    $cart_items_json = isset($_POST["cart_items"]) ? $_POST["cart_items"] : "[]";
    $cart_items = json_decode($cart_items_json, true);
    
    // Format order details in a standardized way for cashier display
    $formatted_details = "";
    
    if (!empty($cart_items) && is_array($cart_items)) {
        // Use the JSON cart items to create a well-formatted order detail
        foreach ($cart_items as $item) {
            if (isset($item['name']) && isset($item['quantity']) && isset($item['price'])) {
                $item_name = mysqli_real_escape_string($conn, $item['name']);
                $quantity = intval($item['quantity']);
                $price = floatval($item['price']);
                $item_total = $price * $quantity;
                
                // Add ingredients if available
                $ingredient_text = "";
                if (isset($item['ingredientPrices']) && !empty($item['ingredientPrices'])) {
                    $ingredient_names = [];
                    foreach ($item['ingredientPrices'] as $ing) {
                        if (isset($ing['name'])) {
                            $ingredient_names[] = $ing['name'];
                        }
                    }
                    if (!empty($ingredient_names)) {
                        $ingredient_text = " (" . implode(", ", $ingredient_names) . ")";
                    }
                }
                
                // Add custom instructions if available
                $custom_instructions = isset($item['customInstructions']) && !empty($item['customInstructions']) 
                    ? " - Note: " . mysqli_real_escape_string($conn, $item['customInstructions']) 
                    : "";
                
                $formatted_details .= "{$item_name} x{$quantity}{$ingredient_text} - ₱{$item_total}{$custom_instructions}\n";
            }
        }
    } else {
        // Fallback to the raw order details if no JSON cart items
        $formatted_details = $raw_order_details;
    }
    
    // Check if the requested table is available (for dine-in orders)
    if ($order_type == "Dine-In" && $table_number !== null) {
        $check_table_sql = "SELECT status FROM tables WHERE table_number = ?";
        $check_stmt = $conn->prepare($check_table_sql);
        
        if ($check_stmt) {
            $check_stmt->bind_param("i", $table_number);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $table_data = $check_result->fetch_assoc();
                
                if ($table_data["status"] != "available") {
                    // Table is not available
                echo json_encode([
                    "success" => false, 
                        "error" => "Table {$table_number} is already occupied.", 
                    "table_unavailable" => true
                ]);
                exit;
            }
            }
            $check_stmt->close();
        }
    }
    
    // Set the order as pending
    $status = "Pending";
    
    // Insert the order into the database
    $sql = "INSERT INTO orders (order_type, total_amount, order_details, table_number, status, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
            $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
        exit;
    }
    
    // Bind parameters with the table number (could be NULL for take-out)
    $stmt->bind_param("sdsss", $order_type, $total_amount, $formatted_details, $table_number, $status);

        if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        
        // For dine-in orders, update the table status to occupied
        if ($order_type == "Dine-In" && $table_number !== null) {
            $table_sql = "UPDATE tables SET status = 'occupied' WHERE table_number = ?";
            $table_stmt = $conn->prepare($table_sql);
            
            if ($table_stmt) {
                $table_stmt->bind_param("i", $table_number);
                $table_stmt->execute();
                $table_stmt->close();
            }
        }
        
        echo json_encode(["success" => true, "order_id" => $order_id]);
    } else {
        echo json_encode(["success" => false, "error" => "Error saving order: " . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}

mysqli_close($conn);
?>
