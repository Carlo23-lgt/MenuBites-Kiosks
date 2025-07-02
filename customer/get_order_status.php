<?php
// /customer/get_order_status.php - Get updated order status
session_start();
include('../includes/config.php');

$order_ids = isset($_SESSION['order']) ? $_SESSION['order'] : [];
if (empty($order_ids)) {
    echo json_encode(['status' => 'No items in your order.']);
    exit;
}

// Get the latest status of the orders
$order_statuses = [];
foreach ($order_ids as $item_id) {
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE item_id = ?");
    $stmt->execute([$item_id]);
    $order = $stmt->fetch();
    $order_statuses[] = $order['status'];
}

// Respond with the current status
echo json_encode(['status' => ucfirst(end($order_statuses))]);
