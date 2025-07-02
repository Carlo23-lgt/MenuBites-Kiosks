// /admin/kds.php - Kitchen Display System (KDS)

<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

include('../includes/header.php');
include('../includes/config.php');

// Fetch orders that are still being prepared
$sql = "SELECT * FROM orders WHERE status != 'completed' ORDER BY id DESC";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
?>

<h2>Kitchen Display System</h2>

<div id="order-list">
    <?php foreach ($orders as $order): ?>
        <div class="order">
            <h4>Order ID: <?php echo $order['id']; ?></h4>
            <ul>
                <?php
                $order_items = json_decode($order['items'], true);
                foreach ($order_items as $item_id) {
                    $stmt = $pdo->prepare("SELECT name FROM menu_items WHERE id = ?");
                    $stmt->execute([$item_id]);
                    $item = $stmt->fetch();
                    echo "<li>{$item['name']}</li>";
                }
                ?>
            </ul>
            <p>Status: <span class="status"><?php echo ucfirst($order['status']); ?></span></p>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Update the KDS every 5 seconds to show new orders
    setInterval(function() {
        location.reload();
    }, 5000);
</script>

<?php include('../includes/footer.php'); ?>
