<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "carlo_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . mysqli_connect_error()]));
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Get order status
    $sql = "SELECT status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]));
    }
    
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "status" => $row['status']
        ]);
    } else {
        // Check if order is in history (finished)
        $history_sql = "SELECT status FROM order_history WHERE id = ?";
        $history_stmt = $conn->prepare($history_sql);
        if (!$history_stmt) {
            die(json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]));
        }
        
        $history_stmt->bind_param("i", $order_id);
        $history_stmt->execute();
        $history_result = $history_stmt->get_result();
        
        if ($history_result->num_rows > 0) {
            $history_row = $history_result->fetch_assoc();
            echo json_encode([
                "success" => true,
                "status" => $history_row['status']
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Order not found"
            ]);
        }
    }
} else {
    echo json_encode([
        "success" => false,
        "error" => "No order ID provided"
    ]);
}
?> 