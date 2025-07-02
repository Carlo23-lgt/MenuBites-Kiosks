// /admin/logout.php - Admin Logout

<?php
session_start();
session_destroy(); // Destroy the session
header("Location: admin.php"); // Redirect to login page
exit;
?>
