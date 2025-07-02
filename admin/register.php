<?php
// /admin/register.php - Admin Registration
include('../includes/config.php');  // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash password before storing in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username already exists
        $sql = "SELECT * FROM admins WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $existingAdmin = $stmt->fetch();

        if ($existingAdmin) {
            $error = "Username already exists!";
        } else {
            // Insert new admin into the database
            $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $hashed_password]);

            // Redirect to the login page after successful registration
            header("Location: admin.php");
            exit;
        }
    }
}
?>

<!-- If error occurs during registration -->
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
