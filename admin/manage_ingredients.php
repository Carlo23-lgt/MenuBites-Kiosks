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

// Handle form submission to add ingredient
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_ingredient'])) {
    $name = trim($_POST['name']);
    $quantity = trim($_POST['quantity']);
    $price = trim($_POST['price']);

    // Check if the ingredient already exists
    $sql = "SELECT COUNT(*) FROM ingredients WHERE name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errorMessage = "Ingredient already exists!";
    } else {
        // Insert into the database
        $sql = "INSERT INTO ingredients (name, quantity, price) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $quantity, $price]);
        $successMessage = "Ingredient added successfully!";
    }
}

// Fetch existing ingredients
$sql = "SELECT * FROM ingredients";
$stmt = $pdo->query($sql);
$ingredients = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="container mt-4">
        <h2>Manage Ingredients</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIngredientModal">Add Ingredient</button>

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
                    <th>Ingredient Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingredients as $ingredient) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($ingredient['name']); ?></td>
                    <td><?php echo htmlspecialchars($ingredient['quantity']); ?></td>
                    <td class="price">₱<?php echo htmlspecialchars(number_format($ingredient['price'], 2))?></td>
                    <td>
                        <a href="delete_ingredient.php?id=<?php echo $ingredient['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ingredient?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Ingredient Modal -->
<div class="modal fade" id="addIngredientModal" tabindex="-1" aria-labelledby="addIngredientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Ingredient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Ingredient Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                    <button type="submit" name="add_ingredient" class="btn btn-primary">Add Ingredient</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('../includes/footer.php'); ?>