<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "carlo_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . mysqli_connect_error()]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST["order_id"];
    $status = $_POST["status"];

    // Update order status
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]));
    }
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        if ($status === "Finished") {
            // Start a transaction
            $conn->begin_transaction();

            try {
                // Get table number
                $table_query = "SELECT table_number FROM orders WHERE id = ?";
                $stmt_table = $conn->prepare($table_query);
                if (!$stmt_table) {
                    throw new Exception("Table query failed: " . $conn->error);
                }
                $stmt_table->bind_param("i", $order_id);
                $stmt_table->execute();
                $result = $stmt_table->get_result();
                if ($result->num_rows == 0) {
                    throw new Exception("Order not found");
                }
                $row = $result->fetch_assoc();
                $table_number = $row["table_number"];

                // Move order to history
                $move_order_sql = "INSERT INTO order_history (id, order_type, total_amount, order_details, table_number, status, created_at)
                                   SELECT id, order_type, total_amount, order_details, table_number, status, created_at
                                   FROM orders WHERE id = ?";
                $stmt_move = $conn->prepare($move_order_sql);
                if (!$stmt_move) {
                    throw new Exception("Move order failed: " . $conn->error);
                }
                $stmt_move->bind_param("i", $order_id);
                $stmt_move->execute();

                // Delete order
                $delete_sql = "DELETE FROM orders WHERE id = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                if (!$stmt_delete) {
                    throw new Exception("Delete failed: " . $conn->error);
                }
                $stmt_delete->bind_param("i", $order_id);
                $stmt_delete->execute();

                // Set table as available
                $update_table_sql = "UPDATE tables SET status = 'available' WHERE table_number = ?";
                $stmt_update_table = $conn->prepare($update_table_sql);
                if (!$stmt_update_table) {
                    throw new Exception("Update table failed: " . $conn->error);
                }
                $stmt_update_table->bind_param("i", $table_number);
                $stmt_update_table->execute();

                // Commit the transaction
                $conn->commit();

                echo json_encode(["success" => true]);
            } catch (Exception $e) {
                // Rollback the transaction if something failed
                $conn->rollback();
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => true]);
        }
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
}
?>