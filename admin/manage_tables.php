<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php"); // Redirect to login if not logged in
    exit;
}
include('../includes/header.php');
include('../includes/config.php');

$successMessage = "";
$errorMessage = "";

// Handle form submission to add table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_table'])) {
    $name = trim($_POST['name']);

    // Check if the table already exists
    $sql = "SELECT COUNT(*) FROM tables WHERE table_number = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errorMessage = "Table already exists!";
    } else {
        // Insert into the database
        $sql = "INSERT INTO tables (table_number) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name]);
        $successMessage = "Table added successfully!";
    }
}

// Handle table deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the table from the database
    $sql = "DELETE FROM tables WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$delete_id])) {
        $successMessage = "Table deleted successfully!";
    } else {
        $errorMessage = "Failed to delete table.";
    }
}

// Fetch existing tables
$sql = "SELECT * FROM tables";
$stmt = $pdo->query($sql);
$tables = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="container mt-4">
        <h2>Manage Tables</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTableModal">Add Table</button>

        <!-- Success Message -->
        <?php if ($successMessage): ?>
        <div class="alert alert-success mt-3">
            <?php echo $successMessage; ?>
        </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($errorMessage): ?>
        <div class="alert alert-danger mt-3">
            <?php echo $errorMessage; ?>
        </div>
        <?php endif; ?>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Table Number</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                    <td><?php echo htmlspecialchars($table['status']); ?></td>
                    <td>
                        <a href="manage_tables.php?delete_id=<?php echo $table['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this table?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" aria-labelledby="addTableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Table Number</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <button type="submit" name="add_table" class="btn btn-primary">Add Table</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('../includes/footer.php'); ?>