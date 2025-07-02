// /customer/order_status.php - Track Customer Order Status

<?php
// /customer/order_status.php - Real-time order status using AJAX
session_start();
include('../includes/config.php');

$order_ids = isset($_SESSION['order']) ? $_SESSION['order'] : [];
if (empty($order_ids)) {
    echo "No items in your order.";
    exit;
}

// Fetch order details and status
$order_details = [];
foreach ($order_ids as $item_id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE item_id = ?");
    $stmt->execute([$item_id]);
    $order_details[] = $stmt->fetch();
}

// Display order items
echo "<h2>Your Order</h2>";
foreach ($order_details as $item) {
    echo "<p>{$item['item_name']} - $" . number_format($item['price'], 2) . "</p>";
}

// Display initial order status
echo "<p>Status: <span id='status'>" . ucfirst($item['status']) . "</span></p>";
?>

<!-- JavaScript for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function checkOrderStatus() {
        $.ajax({
            url: 'get_order_status.php', // Endpoint to fetch the updated status
            method: 'GET',
            success: function(response) {
                $('#status').text(response.status);
            }
        });
    }

    // Check status every 5 seconds
    setInterval(checkOrderStatus, 5000);
</script>
?>