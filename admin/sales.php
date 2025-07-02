<!-- sales.php -->
<?php include('../includes/header.php'); 
include('../includes/config.php'); // Adjust this based on your project structure

// Get total sales (All finished orders)
$query_total = "SELECT SUM(total_amount) AS total_sales FROM order_history";
$stmt_total = $pdo->query($query_total);
$row_total = $stmt_total->fetch();
$total_sales = $row_total['total_sales'] ?? 0;

// Get Dine-In sales
$query_dinein = "SELECT SUM(total_amount) AS dinein_sales FROM order_history WHERE order_type = 'Dine-In'";
$stmt_dinein = $pdo->query($query_dinein);
$row_dinein = $stmt_dinein->fetch();
$dinein_sales = $row_dinein['dinein_sales'] ?? 0;

// Get Take-Out sales
$query_takeout = "SELECT SUM(total_amount) AS takeout_sales FROM order_history WHERE order_type = 'Take-Out'";
$stmt_takeout = $pdo->query($query_takeout);
$row_takeout = $stmt_takeout->fetch();
$takeout_sales = $row_takeout['takeout_sales'] ?? 0;

// Get daily sales (All)
$query_daily = "SELECT SUM(total_amount) AS daily_sales FROM order_history WHERE DATE(created_at) = CURDATE()";
$stmt_daily = $pdo->query($query_daily);
$row_daily = $stmt_daily->fetch();
$daily_sales = $row_daily['daily_sales'] ?? 0;

// Get daily sales for Dine-In
$query_daily_dinein = "SELECT SUM(total_amount) AS daily_dinein FROM order_history WHERE order_type = 'Dine-In' AND DATE(created_at) = CURDATE()";
$stmt_daily_dinein = $pdo->query($query_daily_dinein);
$row_daily_dinein = $stmt_daily_dinein->fetch();
$daily_dinein = $row_daily_dinein['daily_dinein'] ?? 0;

// Get daily sales for Take-Out
$query_daily_takeout = "SELECT SUM(total_amount) AS daily_takeout FROM order_history WHERE order_type = 'Take-Out' AND DATE(created_at) = CURDATE()";
$stmt_daily_takeout = $pdo->query($query_daily_takeout);
$row_daily_takeout = $stmt_daily_takeout->fetch();
$daily_takeout = $row_daily_takeout['daily_takeout'] ?? 0;

// Get monthly sales (All)
$query_monthly = "SELECT SUM(total_amount) AS monthly_sales FROM order_history WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$stmt_monthly = $pdo->query($query_monthly);
$row_monthly = $stmt_monthly->fetch();
$monthly_sales = $row_monthly['monthly_sales'] ?? 0;

// Get monthly sales for Dine-In
$query_monthly_dinein = "SELECT SUM(total_amount) AS monthly_dinein FROM order_history WHERE order_type = 'Dine-In' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$stmt_monthly_dinein = $pdo->query($query_monthly_dinein);
$row_monthly_dinein = $stmt_monthly_dinein->fetch();
$monthly_dinein = $row_monthly_dinein['monthly_dinein'] ?? 0;

// Get monthly sales for Take-Out
$query_monthly_takeout = "SELECT SUM(total_amount) AS monthly_takeout FROM order_history WHERE order_type = 'Take-Out' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$stmt_monthly_takeout = $pdo->query($query_monthly_takeout);
$row_monthly_takeout = $stmt_monthly_takeout->fetch();
$monthly_takeout = $row_monthly_takeout['monthly_takeout'] ?? 0;

// Calculate best selling items from ALL order history
$query_best_sellers = "SELECT order_details FROM order_history ORDER BY created_at DESC";
$stmt_best_sellers = $pdo->query($query_best_sellers);
$orders_for_best_sellers = $stmt_best_sellers->fetchAll();

// Process orders to get item counts for ALL orders
$item_counts = array();
foreach ($orders_for_best_sellers as $order) {
    $order_details = $order['order_details'];
    $items = explode("\n", $order_details);
    
    foreach ($items as $item) {
        if (empty(trim($item))) continue;
        
        // Extract item name (everything before 'x' or '(')
        $item_name = trim($item);
        if (strpos($item_name, ' x') !== false) {
            $item_name = trim(substr($item_name, 0, strpos($item_name, ' x')));
        } else if (strpos($item_name, ' (') !== false) {
            $item_name = trim(substr($item_name, 0, strpos($item_name, ' (')));
        }
        
        if (empty($item_name)) continue;
        
        if (isset($item_counts[$item_name])) {
            $item_counts[$item_name]++;
        } else {
            $item_counts[$item_name] = 1;
        }
    }
}

// Calculate best selling items from DINE-IN order history
$query_dinein_best_sellers = "SELECT order_details FROM order_history WHERE order_type = 'Dine-In' ORDER BY created_at DESC";
$stmt_dinein_best_sellers = $pdo->query($query_dinein_best_sellers);
$dinein_orders_for_best_sellers = $stmt_dinein_best_sellers->fetchAll();

// Process orders to get item counts for DINE-IN orders
$dinein_item_counts = array();
foreach ($dinein_orders_for_best_sellers as $order) {
    $order_details = $order['order_details'];
    $items = explode("\n", $order_details);
    
    foreach ($items as $item) {
        if (empty(trim($item))) continue;
        
        // Extract item name (everything before 'x' or '(')
        $item_name = trim($item);
        if (strpos($item_name, ' x') !== false) {
            $item_name = trim(substr($item_name, 0, strpos($item_name, ' x')));
        } else if (strpos($item_name, ' (') !== false) {
            $item_name = trim(substr($item_name, 0, strpos($item_name, ' (')));
        }
        
        if (empty($item_name)) continue;
        
        if (isset($dinein_item_counts[$item_name])) {
            $dinein_item_counts[$item_name]++;
        } else {
            $dinein_item_counts[$item_name] = 1;
        }
    }
}

// Calculate best selling items from TAKE-OUT order history
$query_takeout_best_sellers = "SELECT order_details FROM order_history WHERE order_type = 'Take-Out' ORDER BY created_at DESC";
$stmt_takeout_best_sellers = $pdo->query($query_takeout_best_sellers);
$takeout_orders_for_best_sellers = $stmt_takeout_best_sellers->fetchAll();

// Process orders to get item counts for TAKE-OUT orders
$takeout_item_counts = array();
foreach ($takeout_orders_for_best_sellers as $order) {
    $order_details = $order['order_details'];
    $items = explode("\n", $order_details);
    
    foreach ($items as $item) {
        if (empty(trim($item))) continue;
        
        // Extract item name (everything before 'x' or '(')
        $item_name = trim($item);
        if (strpos($item_name, ' x') !== false) {
            $item_name = trim(substr($item_name, 0, strpos($item_name, ' x')));
        } else if (strpos($item_name, ' (') !== false) {
            $item_name = trim(substr($item_name, 0, strpos($item_name, ' (')));
        }
        
        if (empty($item_name)) continue;
        
        if (isset($takeout_item_counts[$item_name])) {
            $takeout_item_counts[$item_name]++;
        } else {
            $takeout_item_counts[$item_name] = 1;
        }
    }
}

// Sort by count (descending)
arsort($item_counts);
arsort($dinein_item_counts);
arsort($takeout_item_counts);

// Get top 10 items
$top_items = array_slice($item_counts, 0, 10, true);
$top_dinein_items = array_slice($dinein_item_counts, 0, 10, true);
$top_takeout_items = array_slice($takeout_item_counts, 0, 10, true);

// Fetch all finished orders
$query_orders = "SELECT * FROM order_history ORDER BY created_at DESC";
$stmt_orders = $pdo->query($query_orders);
$orders = $stmt_orders->fetchAll();
?>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .card.clickable {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card.clickable:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .rank-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-weight: bold;
    }
    .rank-1 { background-color: #ffd700; color: #000; }
    .rank-2 { background-color: #c0c0c0; color: #000; }
    .rank-3 { background-color: #cd7f32; color: #fff; }
    .rank-other { background-color: #6c757d; color: #fff; }
    .modal-header.bg-primary { color: white; }
    .modal-header.bg-success { color: white; }
    .modal-header.bg-warning { color: black; }
</style>
</head>
<body>
<div class="content-wrapper">
    <div class="container mt-4">
    <h2 class="text-center mb-4">📊 Sales Report</h2>

    <!-- Sales Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 clickable" id="total-sales-card" data-bs-toggle="modal" data-bs-target="#bestSellersModal">
                <div class="card-header">Total Sales (Click to see best sellers)</div>
                <div class="card-body">
                    <h4 class="card-title">₱<?= number_format($total_sales, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 clickable" id="dinein-sales-card" data-bs-toggle="modal" data-bs-target="#dineinBestSellersModal">
                <div class="card-header">Dine-In Sales (Click to see best sellers)</div>
                <div class="card-body">
                    <h4 class="card-title">₱<?= number_format($dinein_sales, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3 clickable" id="takeout-sales-card" data-bs-toggle="modal" data-bs-target="#takeoutBestSellersModal">
                <div class="card-header">Take-Out Sales (Click to see best sellers)</div>
                <div class="card-body">
                    <h4 class="card-title">₱<?= number_format($takeout_sales, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily & Monthly Sales Breakdown -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Daily Sales (Today)</h5>
            <p>Total: ₱<?= number_format($daily_sales, 2) ?> | Dine-In: ₱<?= number_format($daily_dinein, 2) ?> | Take-Out: ₱<?= number_format($daily_takeout, 2) ?></p>
        </div>
        <div class="col-md-6">
            <h5>Monthly Sales (This Month)</h5>
            <p>Total: ₱<?= number_format($monthly_sales, 2) ?> | Dine-In: ₱<?= number_format($monthly_dinein, 2) ?> | Take-Out: ₱<?= number_format($monthly_takeout, 2) ?></p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header bg-dark text-white">Finished Orders</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Type</th>
                        <th>Total Amount</th>
                        <th>Table</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= $order['order_type'] ?></td>
                            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                            <td><?= $order['table_number'] ? $order['table_number'] : 'N/A' ?></td>
                            <td><?= date("F j, Y g:i A", strtotime($order['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-info btn-sm view-order-details" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#orderDetailsModal"
                                        data-order-id="<?= $order['id'] ?>"
                                        data-order-type="<?= $order['order_type'] ?>"
                                        data-total="<?= $order['total_amount'] ?>"
                                        data-table="<?= $order['table_number'] ?>"
                                        data-date="<?= date("F j, Y g:i A", strtotime($order['created_at'])) ?>"
                                        data-details="<?= htmlspecialchars($order['order_details']) ?>">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<!-- TOTAL Best Sellers Modal -->
<div class="modal fade" id="bestSellersModal" tabindex="-1" aria-labelledby="bestSellersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="bestSellersModalLabel">🏆 Best Selling Items (All Orders)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($top_items)): ?>
                    <p class="text-center">No sales data available yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Item Name</th>
                                    <th>Times Ordered</th>
                                    <th>Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                // Get the highest count for calculating percentage
                                $max_count = reset($top_items);
                                
                                foreach ($top_items as $item_name => $count): 
                                    $rankClass = $rank <= 3 ? "rank-{$rank}" : "rank-other";
                                    $percentage = ($count / $max_count) * 100;
                                ?>
                                    <tr>
                                        <td>
                                            <span class="rank-badge <?= $rankClass ?>"><?= $rank ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($item_name) ?></strong></td>
                                        <td><?= $count ?> orders</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: <?= $percentage ?>%" 
                                                     aria-valuenow="<?= $percentage ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?= round($percentage) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                $rank++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- DINE-IN Best Sellers Modal -->
<div class="modal fade" id="dineinBestSellersModal" tabindex="-1" aria-labelledby="dineinBestSellersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="dineinBestSellersModalLabel">🏆 Best Selling Items (Dine-In Only)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($top_dinein_items)): ?>
                    <p class="text-center">No Dine-In sales data available yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Item Name</th>
                                    <th>Times Ordered</th>
                                    <th>Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                // Get the highest count for calculating percentage
                                $max_count = reset($top_dinein_items);
                                
                                foreach ($top_dinein_items as $item_name => $count): 
                                    $rankClass = $rank <= 3 ? "rank-{$rank}" : "rank-other";
                                    $percentage = ($count / $max_count) * 100;
                                ?>
                                    <tr>
                                        <td>
                                            <span class="rank-badge <?= $rankClass ?>"><?= $rank ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($item_name) ?></strong></td>
                                        <td><?= $count ?> orders</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?= $percentage ?>%" 
                                                     aria-valuenow="<?= $percentage ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?= round($percentage) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                $rank++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- TAKE-OUT Best Sellers Modal -->
<div class="modal fade" id="takeoutBestSellersModal" tabindex="-1" aria-labelledby="takeoutBestSellersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="takeoutBestSellersModalLabel">🏆 Best Selling Items (Take-Out Only)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($top_takeout_items)): ?>
                    <p class="text-center">No Take-Out sales data available yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Item Name</th>
                                    <th>Times Ordered</th>
                                    <th>Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                // Get the highest count for calculating percentage
                                $max_count = reset($top_takeout_items);
                                
                                foreach ($top_takeout_items as $item_name => $count): 
                                    $rankClass = $rank <= 3 ? "rank-{$rank}" : "rank-other";
                                    $percentage = ($count / $max_count) * 100;
                                ?>
                                    <tr>
                                        <td>
                                            <span class="rank-badge <?= $rankClass ?>"><?= $rank ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($item_name) ?></strong></td>
                                        <td><?= $count ?> orders</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" role="progressbar" 
                                                     style="width: <?= $percentage ?>%" 
                                                     aria-valuenow="<?= $percentage ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?= round($percentage) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                $rank++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Order ID:</strong> <span id="modal-order-id"></span></p>
                        <p><strong>Order Type:</strong> <span id="modal-order-type"></span></p>
                        <p><strong>Table Number:</strong> <span id="modal-table"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Amount:</strong> ₱<span id="modal-total"></span></p>
                        <p><strong>Date & Time:</strong> <span id="modal-date"></span></p>
                    </div>
                </div>
                <h6 class="border-top pt-3">Order Items:</h6>
                <div class="table-responsive">
                    <table class="table table-striped" id="order-items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Order items will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-order-details">Print Order</button>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Handle order details modal
    document.querySelectorAll('.view-order-details').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderType = this.getAttribute('data-order-type');
            const total = parseFloat(this.getAttribute('data-total')).toFixed(2);
            const table = this.getAttribute('data-table');
            const date = this.getAttribute('data-date');
            const details = this.getAttribute('data-details');
            
            // Update modal content
            document.getElementById('modal-order-id').textContent = orderId;
            document.getElementById('modal-order-type').textContent = orderType;
            document.getElementById('modal-total').textContent = total;
            document.getElementById('modal-table').textContent = table || 'N/A';
            document.getElementById('modal-date').textContent = date;
            
            // Parse and format order details into a table
            const itemsTable = document.getElementById('order-items-table').getElementsByTagName('tbody')[0];
            itemsTable.innerHTML = ''; // Clear previous content
            
            if (details && details.trim()) {
                // Split the details by line breaks and remove empty lines
                const lines = details.split('\n').filter(line => line.trim());
                
                lines.forEach(line => {
                    if (line.trim()) {
                        // Create a row for each line
                        const row = itemsTable.insertRow();
                        
                        // Format: "Item Name x1 () - ₱180.00" or similar
                        const itemMatch = line.match(/(.+?)\s*x(\d+)\s*(?:\(([^)]*)\))?\s*-\s*₱([\d.]+)(.*)/i);
                        
                        if (itemMatch) {
                            // Standard format match
                            const itemName = itemMatch[1].trim();
                            const quantity = itemMatch[2];
                            const ingredients = itemMatch[3] ? itemMatch[3].trim() : '';
                            const price = itemMatch[4];
                            const notes = itemMatch[5] ? itemMatch[5].trim().replace(/^-?\s*Note:/i, '').trim() : '';
                            
                            row.insertCell(0).textContent = itemName;
                            row.insertCell(1).textContent = quantity;
                            row.insertCell(2).textContent = `₱${price}`;
                            
                            // Combine ingredients and notes
                            let additionalInfo = [];
                            if (ingredients) additionalInfo.push(ingredients);
                            if (notes) additionalInfo.push(`Note: ${notes}`);
                            
                            row.insertCell(3).textContent = additionalInfo.join(' | ');
                        } else {
                            // Just display the line as is across all columns
                            const cell = row.insertCell(0);
                            cell.colSpan = 4;
                            cell.textContent = line;
                        }
                    }
                });
            } else {
                // No details available
                const row = itemsTable.insertRow();
                const cell = row.insertCell(0);
                cell.colSpan = 4;
                cell.textContent = 'No order details available';
                cell.className = 'text-center';
            }
        });
    });
    
    // Print order functionality
    document.getElementById('print-order-details').addEventListener('click', function() {
        const orderId = document.getElementById('modal-order-id').textContent;
        const orderType = document.getElementById('modal-order-type').textContent;
        const total = document.getElementById('modal-total').textContent;
        const table = document.getElementById('modal-table').textContent;
        const date = document.getElementById('modal-date').textContent;
        
        // Get order items
        const itemsTable = document.getElementById('order-items-table').outerHTML;
        
        // Create print window
        const printWindow = window.open('', '_blank', 'height=600,width=800');
        
        // Write HTML content in separate statements
        printWindow.document.write('<html>');
        printWindow.document.write('<head>');
        printWindow.document.write('<title>Order #' + orderId + ' Receipt</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
        printWindow.document.write('h2 { text-align: center; }');
        printWindow.document.write('.order-info { margin-bottom: 20px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }');
        printWindow.document.write('th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }');
        printWindow.document.write('th { background-color: #f2f2f2; }');
        printWindow.document.write('.total { font-weight: bold; text-align: right; }');
        printWindow.document.write('@media print { button { display: none; } }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head>');
        printWindow.document.write('<body>');
        printWindow.document.write('<h2>D Breakers Restobar - Order Receipt</h2>');
        printWindow.document.write('<div class="order-info">');
        printWindow.document.write('<p><strong>Order ID:</strong> ' + orderId + '</p>');
        printWindow.document.write('<p><strong>Order Type:</strong> ' + orderType + '</p>');
        printWindow.document.write('<p><strong>Table:</strong> ' + table + '</p>');
        printWindow.document.write('<p><strong>Date & Time:</strong> ' + date + '</p>');
        printWindow.document.write('</div>');
        printWindow.document.write('<h3>Order Items</h3>');
        printWindow.document.write(itemsTable);
        printWindow.document.write('<p class="total">Total Amount: ₱' + total + '</p>');
        printWindow.document.write('<div style="text-align: center; margin-top: 30px;">');
        printWindow.document.write('<button onclick="window.print()">Print Receipt</button>');
        printWindow.document.write('</div>');
        printWindow.document.write('<script>');
        printWindow.document.write('window.onload = function() { window.print(); }');
        printWindow.document.write('<\/script>');
        printWindow.document.write('</body>');
        printWindow.document.write('</html>');
        
        printWindow.document.close();
    });
</script>
</body>
</html>