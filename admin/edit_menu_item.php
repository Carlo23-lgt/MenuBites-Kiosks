<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php"); // Redirect to login if not logged in
    exit;
}
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_menu_item'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    // Image Upload Handling
    $targetDir = "../assets/images/";
    $imageName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $imageName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed image formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!empty($imageName)) {
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Update the menu item with the new image
                $sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName, $id]);
            } else {
                $_SESSION['error_message'] = "Error uploading the image.";
                header("Location: manage_menu.php");
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Invalid image format. Allowed: JPG, JPEG, PNG, GIF.";
            header("Location: manage_menu.php");
            exit;
        }
    } else {
        // Update the menu item without changing the image
        $sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, stock = ?, category_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $stock, $category_id, $id]);
    }

    $_SESSION['success_message'] = "Menu item updated successfully!";
    header("Location: manage_menu.php");
    exit;
} else {
    $_SESSION['error_message'] = "Invalid request!";
    header("Location: manage_menu.php");
    exit;
}
?>