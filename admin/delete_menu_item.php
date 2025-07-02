<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php"); // Redirect to login if not logged in
    exit;
}
include('../includes/config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the image name to delete the file
    $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if ($item) {
        // Delete the image file
        $imagePath = "../assets/images/" . $item['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete the menu item from the database
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success_message'] = "Menu item deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Menu item not found!";
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
}

header("Location: manage_menu.php");
exit;
?>