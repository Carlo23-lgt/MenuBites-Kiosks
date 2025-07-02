<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your DB username
$password = ""; // Your DB password
$database = "carlo_db"; // Your database name

$conn = mysqli_connect($servername, $username, $password, $database);

header('Content-Type: application/json');

$query = "SELECT * FROM tables WHERE status = 'available' "; // Fetch all tables
$result = $conn->query($query);

$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

echo json_encode($tables);
?>

