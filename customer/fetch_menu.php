<?php
include('../includes/config.php'); // Include your database connection

// Fetch categories
$sql_categories = "SELECT * FROM categories ORDER BY name";
$stmt_categories = $pdo->query($sql_categories);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Fetch menu items
$sql_items = "SELECT * FROM menu_items ORDER BY name";
$stmt_items = $pdo->query($sql_items);
$menu_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

// Return data as JSON
echo json_encode([
    'categories' => $categories,
    'menu_items' => $menu_items
]);
?>
