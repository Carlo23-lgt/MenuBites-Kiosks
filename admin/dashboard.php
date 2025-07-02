<!-- dashboard.php -->
<?php include('../includes/header.php'); 
include('../includes/config.php'); // Adjust based on your project structure

// Get daily sales data (last 7 days)
$query_daily = "SELECT DATE(created_at) AS order_date, SUM(total_amount) AS total_sales, COUNT(id) AS total_customers
                FROM order_history 
                GROUP BY DATE(created_at) 
                ORDER BY order_date DESC 
                LIMIT 7";
$stmt_daily = $pdo->query($query_daily);
$daily_sales_data = $stmt_daily->fetchAll(PDO::FETCH_ASSOC);

// Get monthly sales data (last 6 months)
$query_monthly = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_amount) AS total_sales 
                  FROM order_history 
                  GROUP BY month 
                  ORDER BY month DESC 
                  LIMIT 6";
$stmt_monthly = $pdo->query($query_monthly);
$monthly_sales_data = $stmt_monthly->fetchAll(PDO::FETCH_ASSOC);

// Get yearly sales data (last 5 years)
$query_yearly = "SELECT YEAR(created_at) AS year, SUM(total_amount) AS total_sales 
                 FROM order_history 
                 GROUP BY year 
                 ORDER BY year DESC 
                 LIMIT 5";
$stmt_yearly = $pdo->query($query_yearly);
$yearly_sales_data = $stmt_yearly->fetchAll(PDO::FETCH_ASSOC);

// Get best selling items BY MONTH
function getBestSellersByMonth($pdo, $limit = 5) {
    // Get available months from order_history
    $months_query = "SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') AS month 
                    FROM order_history 
                    ORDER BY month DESC 
                    LIMIT 12";
    $months_stmt = $pdo->query($months_query);
    $available_months = $months_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $result = [];
    
    foreach ($available_months as $month) {
        // Get all orders for this month
        $orders_query = "SELECT order_details FROM order_history 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? 
                        ORDER BY created_at";
        $orders_stmt = $pdo->prepare($orders_query);
        $orders_stmt->execute([$month]);
        $orders = $orders_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Process orders to get item counts
        $item_counts = [];
        foreach ($orders as $order_details) {
            $lines = explode("\n", $order_details);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                // Extract item name (everything before 'x' or '(')
                $item_name = trim($line);
                if (strpos($item_name, ' x') !== false) {
                    $item_name = trim(substr($item_name, 0, strpos($item_name, ' x')));
                } else if (strpos($item_name, ' (') !== false) {
                    $item_name = trim(substr($item_name, 0, strpos($item_name, ' (')));
                }
                
                if (empty($item_name)) continue;
                
                // Extract quantity if available
                $quantity = 1;
                if (preg_match('/x(\d+)/', $line, $matches)) {
                    $quantity = intval($matches[1]);
                }
                
                if (isset($item_counts[$item_name])) {
                    $item_counts[$item_name] += $quantity;
                } else {
                    $item_counts[$item_name] = $quantity;
                }
            }
        }
        
        // Sort by count (highest first)
        arsort($item_counts);
        
        // Format month for display
        $display_month = date('F Y', strtotime($month . '-01'));
        
        // Add to results
        $result[$display_month] = array_slice($item_counts, 0, $limit, true);
    }
    
    return $result;
}

// Get best selling items BY YEAR
function getBestSellersByYear($pdo, $limit = 5) {
    // Get available years from order_history
    $years_query = "SELECT DISTINCT YEAR(created_at) AS year 
                   FROM order_history 
                   ORDER BY year DESC 
                   LIMIT 5";
    $years_stmt = $pdo->query($years_query);
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $result = [];
    
    foreach ($available_years as $year) {
        // Get all orders for this year
        $orders_query = "SELECT order_details FROM order_history 
                        WHERE YEAR(created_at) = ? 
                        ORDER BY created_at";
        $orders_stmt = $pdo->prepare($orders_query);
        $orders_stmt->execute([$year]);
        $orders = $orders_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Process orders to get item counts
        $item_counts = [];
        foreach ($orders as $order_details) {
            $lines = explode("\n", $order_details);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                // Extract item name (everything before 'x' or '(')
                $item_name = trim($line);
                if (strpos($item_name, ' x') !== false) {
                    $item_name = trim(substr($item_name, 0, strpos($item_name, ' x')));
                } else if (strpos($item_name, ' (') !== false) {
                    $item_name = trim(substr($item_name, 0, strpos($item_name, ' (')));
                }
                
                if (empty($item_name)) continue;
                
                // Extract quantity if available
                $quantity = 1;
                if (preg_match('/x(\d+)/', $line, $matches)) {
                    $quantity = intval($matches[1]);
                }
                
                if (isset($item_counts[$item_name])) {
                    $item_counts[$item_name] += $quantity;
                } else {
                    $item_counts[$item_name] = $quantity;
                }
            }
        }
        
        // Sort by count (highest first)
        arsort($item_counts);
        
        // Add to results
        $result[$year] = array_slice($item_counts, 0, $limit, true);
    }
    
    return $result;
}

// Get the data for monthly and yearly best sellers
$monthly_best_sellers = getBestSellersByMonth($pdo);
$yearly_best_sellers = getBestSellersByYear($pdo);

// Convert PHP data to JSON for Chart.js
$daily_labels = array_reverse(array_column($daily_sales_data, 'order_date'));
$daily_sales_values = array_reverse(array_column($daily_sales_data, 'total_sales'));
$daily_customers_values = array_reverse(array_column($daily_sales_data, 'total_customers'));

$monthly_labels = array_reverse(array_column($monthly_sales_data, 'month'));
$monthly_values = array_reverse(array_column($monthly_sales_data, 'total_sales'));

$yearly_labels = array_reverse(array_column($yearly_sales_data, 'year'));
$yearly_values = array_reverse(array_column($yearly_sales_data, 'total_sales'));
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports (Graph)</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .rank-badge {
            width: 25px;
            height: 25px;
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
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }
        .chart-container {
            position: relative;
            height: 350px;
        }
    </style>
</head>

<div class="content-wrapper">
    <div class="container mt-4">
    <h2 class="text-center mb-4">📊 Sales & Customer Reports</h2>

    <div class="row">
        <!-- Daily Sales Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Daily Sales (Last 7 Days)</div>
                <div class="card-body">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Customers Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">Daily Customers (Last 7 Days)</div>
                <div class="card-body">
                    <canvas id="dailyCustomersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Sales Chart -->
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header bg-success text-white">Monthly Sales (Last 6 Months)</div>
                <div class="card-body">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Yearly Sales Chart -->
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header bg-warning text-white">Yearly Sales (Last 5 Years)</div>
                <div class="card-body">
                    <canvas id="yearlySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Best Selling Items by Month and Year -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">🏆 Best Selling Menu Items</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="best-sellers-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly-best-sellers" type="button" role="tab">Monthly Best Sellers</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="yearly-tab" data-bs-toggle="pill" data-bs-target="#yearly-best-sellers" type="button" role="tab">Yearly Best Sellers</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="best-sellers-content">
                        <!-- Monthly Best Sellers -->
                        <div class="tab-pane fade show active" id="monthly-best-sellers" role="tabpanel">
                            <div class="accordion" id="accordionMonthly">
                                <?php 
                                $count = 0;
                                foreach ($monthly_best_sellers as $month => $items): 
                                    $count++;
                                    $show = ($count === 1) ? 'show' : '';
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button <?= ($count !== 1) ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-month-<?= $count ?>">
                                            Best Sellers: <?= $month ?>
                                        </button>
                                    </h2>
                                    <div id="collapse-month-<?= $count ?>" class="accordion-collapse collapse <?= $show ?>" data-bs-parent="#accordionMonthly">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <div class="chart-container">
                                                        <canvas id="monthly-best-chart-<?= $count ?>"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Rank</th>
                                                                    <th>Item</th>
                                                                    <th>Quantity</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $rank = 1; foreach ($items as $item_name => $quantity): ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="rank-badge <?= ($rank <= 3) ? 'rank-' . $rank : 'rank-other' ?>">
                                                                            <?= $rank ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($item_name) ?></td>
                                                                    <td><?= $quantity ?></td>
                                                                </tr>
                                                                <?php $rank++; endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Yearly Best Sellers -->
                        <div class="tab-pane fade" id="yearly-best-sellers" role="tabpanel">
                            <div class="accordion" id="accordionYearly">
                                <?php 
                                $count = 0;
                                foreach ($yearly_best_sellers as $year => $items): 
                                    $count++;
                                    $show = ($count === 1) ? 'show' : '';
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button <?= ($count !== 1) ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-year-<?= $count ?>">
                                            Best Sellers: <?= $year ?>
                                        </button>
                                    </h2>
                                    <div id="collapse-year-<?= $count ?>" class="accordion-collapse collapse <?= $show ?>" data-bs-parent="#accordionYearly">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <div class="chart-container">
                                                        <canvas id="yearly-best-chart-<?= $count ?>"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Rank</th>
                                                                    <th>Item</th>
                                                                    <th>Quantity</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $rank = 1; foreach ($items as $item_name => $quantity): ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="rank-badge <?= ($rank <= 3) ? 'rank-' . $rank : 'rank-other' ?>">
                                                                            <?= $rank ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($item_name) ?></td>
                                                                    <td><?= $quantity ?></td>
                                                                </tr>
                                                                <?php $rank++; endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     
    </div>
</div>

<!-- Chart.js Script -->
<script>
    // Daily Sales Chart
    var ctx1 = document.getElementById('dailySalesChart').getContext('2d');
    var dailySalesChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?= json_encode($daily_labels) ?>,
            datasets: [{
                label: 'Daily Sales (₱)',
                data: <?= json_encode($daily_sales_values) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Daily Customers Chart
    var ctx2 = document.getElementById('dailyCustomersChart').getContext('2d');
    var dailyCustomersChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?= json_encode($daily_labels) ?>,
            datasets: [{
                label: 'Daily Customers (Orders)',
                data: <?= json_encode($daily_customers_values) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Monthly Sales Chart
    var ctx3 = document.getElementById('monthlySalesChart').getContext('2d');
    var monthlySalesChart = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: <?= json_encode($monthly_labels) ?>,
            datasets: [{
                label: 'Monthly Sales (₱)',
                data: <?= json_encode($monthly_values) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Yearly Sales Chart
    var ctx4 = document.getElementById('yearlySalesChart').getContext('2d');
    var yearlySalesChart = new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: <?= json_encode($yearly_labels) ?>,
            datasets: [{
                label: 'Yearly Sales (₱)',
                data: <?= json_encode($yearly_values) ?>,
                backgroundColor: 'rgba(255, 206, 86, 0.5)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Generate charts for monthly best sellers
    <?php $count = 0; foreach ($monthly_best_sellers as $month => $items): $count++; ?>
    var monthlyCtx<?= $count ?> = document.getElementById('monthly-best-chart-<?= $count ?>').getContext('2d');
    var monthlyChart<?= $count ?> = new Chart(monthlyCtx<?= $count ?>, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($items)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($items)) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Top Selling Items: <?= $month ?>'
                }
            }
        }
    });
    <?php endforeach; ?>

    // Generate charts for yearly best sellers
    <?php $count = 0; foreach ($yearly_best_sellers as $year => $items): $count++; ?>
    var yearlyCtx<?= $count ?> = document.getElementById('yearly-best-chart-<?= $count ?>').getContext('2d');
    var yearlyChart<?= $count ?> = new Chart(yearlyCtx<?= $count ?>, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($items)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($items)) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Top Selling Items: <?= $year ?>'
                }
            }
        }
    });
    <?php endforeach; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('../includes/footer.php'); ?>