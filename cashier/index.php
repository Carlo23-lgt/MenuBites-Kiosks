<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your DB username
$password = ""; // Your DB password
$database = "carlo_db"; // Your database name

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all orders from the database in ascending order
$result = $conn->query("SELECT * FROM orders ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .status-pending { color: red; font-weight: bold; }
        .status-preparing { color: orange; font-weight: bold; }
        .status-finished { color: green; font-weight: bold; }
        .order-row {
            cursor: pointer;
        }
        .order-row:hover {
            background-color: #e9ecef;
        }
        .order-details-content {
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="mb-4">Cashier Dashboard - Manage Orders</h3>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Order Type</th>
                    <th>Total Amount</th>
                    <th>Table number</th>
                    <th>Order Details</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['order_type'] ?></td>
                        <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                        <td><?= $row['table_number'] ?></td>
                        <td><?= nl2br($row['order_details']) ?></td>
                        <td>
                            <span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span>
                        </td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <select class="form-select status-dropdown" data-order-id="<?= $row['id'] ?>">
                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Preparing" <?= $row['status'] == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                                <option value="Finished" <?= $row['status'] == 'Finished' ? 'selected' : '' ?>>Finished</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll(".status-dropdown").forEach(select => {
            select.addEventListener("change", function (event) {
                let orderId = this.getAttribute("data-order-id");
                let newStatus = this.value;

                fetch("update_order_status.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `order_id=${orderId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Order status updated!");
                        location.reload(); // Refresh page to reflect changes
                    } else {
                        alert("Error updating order: " + data.error);
                    }
                });
            });
        });
    </script>
</body>
</html>