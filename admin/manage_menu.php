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

// Handle form submission to add menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu_item'])) {
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

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Insert into database
            $sql = "INSERT INTO menu_items (name, description, price, stock, category_id, image) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName]);

            $successMessage = "Menu item added successfully!";
        } else {
            $errorMessage = "Error uploading the image.";
        }
    } else {
        $errorMessage = "Invalid image format. Allowed: JPG, JPEG, PNG, GIF.";
    }
}

// Handle form submission to edit menu item
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
                // Update database with new image
                $sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName, $id]);

                $successMessage = "Menu item updated successfully!";
            } else {
                $errorMessage = "Error uploading the image.";
            }
        } else {
            $errorMessage = "Invalid image format. Allowed: JPG, JPEG, PNG, GIF.";
        }
    } else {
        // Update database without new image
        $sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, stock = ?, category_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $stock, $category_id, $id]);

        $successMessage = "Menu item updated successfully!";
    }
}

// Fetch existing menu items with category names
$sql = "SELECT menu_items.*, categories.name AS category_name FROM menu_items 
        JOIN categories ON menu_items.category_id = categories.id";
$stmt = $pdo->query($sql);
$menu_items = $stmt->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="content-wrapper">
    <div class="container mt-4">
        <h2>Manage Menu</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">Add Menu Item</button>

        <?php if ($successMessage): ?>
        <div class="alert alert-success mt-3">
            <?php echo $successMessage; ?>
        </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
        <div class="alert alert-danger mt-3">
            <?php echo $errorMessage; ?>
        </div>
        <?php endif; ?>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu_items as $item) : ?>
                <tr>
                    <td>
                        <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="Menu Image" width="50">
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($item['stock']); ?></td>
                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editMenuModal<?php echo $item['id']; ?>">Edit</button>
                        <a href="delete_menu_item.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>

                <!-- Edit Menu Item Modal -->
                <div class="modal fade" id="editMenuModal<?php echo $item['id']; ?>" tabindex="-1" aria-labelledby="editMenuModalLabel<?php echo $item['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Menu Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control" name="price" step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="number" class="form-control" name="stock" value="<?php echo htmlspecialchars($item['stock']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category_id">
                                            <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $item['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Upload Image</label>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                    </div>
                                    <button type="submit" name="edit_menu_item" class="btn btn-primary">Update Menu Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Menu Item Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id">
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_menu_item" class="btn btn-primary">Add Menu Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('../includes/footer.php'); ?>