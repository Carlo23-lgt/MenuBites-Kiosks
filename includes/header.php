<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Sidebar styling */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #8B5A2B;
            padding-top: 10px;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar img {
            display: block;
            margin: 0 auto 10px auto;
            width: 150px;
            height: auto;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            display: block;
            font-size: 18px;
            transition: background 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #1d1f2d;
        }
        .sidebar .logo {
            font-size: 22px;
            color: white;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .content-wrapper {
            margin-left: 250px;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="../assets/Logo.png" alt="Logo">
        <div class="logo">
            D'Breaker's Admin
        </div>
        <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="manage_menu.php"><i class="fa fa-cutlery"></i> Manage Menu</a>
        <a href="manage_tables.php"><i class="fa fa-table"></i> Manage Tables</a>
        <a href="manage_category.php"><i class="fa fa-book"></i> Manage Categories</a>
        <a href="manage_ingredients.php"><i class="fa fa-lemon-o"></i> Manage Ingredients</a>
        <a href="sales.php"><i class="fa fa-bar-chart"></i> Sales</a>
        <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>

        <!-- Profile Link in Sidebar -->
        <a href="edit_profile.php" class="profile-link"><i class="fa fa-user"></i> Edit Profile</a>
    </div>

    <script>
        // Get current page URL
        const currentUrl = window.location.href;
        
        // Select all sidebar links
        const navLinks = document.querySelectorAll('.sidebar a');

        // Loop through links and check if href matches current URL
        navLinks.forEach(link => {
            if (link.href === currentUrl) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>