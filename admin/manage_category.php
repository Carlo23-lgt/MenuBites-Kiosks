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

// Handle form submission to add category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);

    // Check if the category already exists
    $sql = "SELECT COUNT(*) FROM categories WHERE name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errorMessage = "Category already exists!";
    } else {
        // Insert into the database
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name]);
        $successMessage = "Category added successfully!";
    }
}

// Fetch existing categories
$sql = "SELECT * FROM categories";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="container mt-4">
        <h2>Manage Categories</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">Add Category</button>

        <!-- Success Message Modal -->
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
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('../includes/footer.php'); ?>
