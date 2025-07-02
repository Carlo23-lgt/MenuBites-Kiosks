<?php
session_start();

// Initialize cart session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the data is being posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item'])) {
    // Decode the posted cart item
    $item = json_decode($_POST['item'], true);

    // Add the item to the session cart
    $_SESSION['cart'][] = $item;

    // Optionally, return the updated cart to the frontend as a JSON response
    echo json_encode($_SESSION['cart']);
    exit;
}
