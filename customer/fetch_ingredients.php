<?php
include('../includes/config.php'); // Include your database connection

// Fetch ingredients with quantity information
$sql_ingredients = "SELECT * FROM ingredients WHERE quantity > 0 ORDER BY name";
$stmt_ingredients = $pdo->query($sql_ingredients);
$ingredients = $stmt_ingredients->fetchAll(PDO::FETCH_ASSOC);

// Return data as JSON
header('Content-Type: application/json');
echo json_encode([
    'ingredients' => $ingredients
]);
?> 