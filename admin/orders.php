// /admin/orders.php - View Orders

<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

include('../includes/header.php');
include('../includes/config.php');

// Fetch all orders
$sql = "SELECT * FROM orders WHERE status != 'completed'"; // Orders that are still being processed
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
?>

<h2>View Orders</h2>

<table class="table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Items</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td>
                <?php
                $order_items = json_decode($order['items'], true);
                foreach ($order_items as $item_id) {
                    $stmt = $pdo->prepare("SELECT name FROM menu_items WHERE id = ?");
                    $stmt->execute([$item_id]);
                    $item = $stmt->fetch();
                    echo $item['name'] . "<br>";
                }
                ?>
            </td>
            <td><?php echo $order['customer_name']; ?></td>
            <td>
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status">
                        <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="preparing" <?php echo ($order['status'] == 'preparing') ? 'selected' : ''; ?>>Preparing</option>
                        <option value="ready" <?php echo ($order['status'] == 'ready') ? 'selected' : ''; ?>>Ready</option>
                        <option value="served" <?php echo ($order['status'] == 'served') ? 'selected' : ''; ?>>Served</option>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>
