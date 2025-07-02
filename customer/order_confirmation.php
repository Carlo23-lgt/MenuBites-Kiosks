<?php
session_start();
include('../includes/config.php');

// Get the order ID from the URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if ($order_id) {
    // Fetch the order details
    $sql_order = "SELECT * FROM orders WHERE id = ?";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$order_id]);
    $order = $stmt_order->fetch();

    // Fetch the items for this order
    $sql_items = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute([$order_id]);
    $items = $stmt_items->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>

    <p>Order ID: <?php echo $order['id']; ?></p>
    <p>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></p>
    <p>Dine Option: <?php echo $order['dine_option']; ?></p>

    <h3>Order Items</h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <?php echo htmlspecialchars($item['item_name']); ?> - $<?php echo number_format($item['item_price'], 2); ?> x <?php echo $item['quantity']; ?>
                <?php if ($item['extra_ingredients']): ?>
                    <br><strong>Extra Ingredients:</strong> <?php echo htmlspecialchars($item['extra_ingredients']); ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <p>Status: <?php echo $order['status']; ?></p>

</body>
</html>
