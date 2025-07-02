<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php"); // Redirect to login if not logged in
    exit;
}
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the ingredient from the database
    $sql = "DELETE FROM ingredients WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: manage_ingredients.php");
    exit;
}
?>