<?php
// /customer/order.php - Add items to cart
session_start();
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $menu_item_id = $_POST['add_to_cart'];

    // Add item to session order
    if (!isset($_SESSION['order'])) {
        $_SESSION['order'] = [];
    }
    $_SESSION['order'][] = $menu_item_id;

    // Redirect to order status page
    header("Location: order_status.php");
    exit;
}
?>
