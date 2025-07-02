<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php"); // Redirect to login if not logged in
    exit;
}

include('../includes/config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the category is linked to any menu items
    $sql = "SELECT COUNT(*) FROM menu_items WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['error'] = "Cannot delete this category. It is assigned to $count menu item(s).";
    } else {
        // Delete the category if no linked menu items
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $_SESSION['success'] = "Category deleted successfully!";
    }
} else {
    $_SESSION['error'] = "Invalid request!";
}

header("Location: manage_category.php");
exit;
