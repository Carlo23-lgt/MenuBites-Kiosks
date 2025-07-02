// /admin/update_order_status.php - Update order status

<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Update the order status
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    // Redirect back to the orders page
    header("Location: orders.php");
    exit;
}
?>
