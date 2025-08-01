<?php
session_start();
require_once 'db.php';

// Get total products
$product_count = 0;
$res = pg_query($conn, 'SELECT COUNT(*) FROM products');
if ($res) {
    $row = pg_fetch_row($res);
    $product_count = $row[0];
}

// Get total sales
$sales_count = 0;
$res = pg_query($conn, 'SELECT COUNT(*) FROM sales');
if ($res) {
    $row = pg_fetch_row($res);
    $sales_count = $row[0];
}

// Get cart count for current user (from session)
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f6fb; }
        .dashboard-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            transition: box-shadow 0.2s;
        }
        .dashboard-card:hover {
            box-shadow: 0 6px 32px rgba(0,0,0,0.12);
        }
        .dashboard-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .dashboard-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6c757d;
        }
        .dashboard-value {
            font-size: 2.2rem;
            font-weight: 700;
        }
        .dashboard-link {
            text-decoration: none;
            color: #0d6efd;
            font-weight: 500;
        }
        .dashboard-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Computer Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="mycart.php">My Cart</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="sales.php">Sales</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <h1 class="mb-4 fw-bold text-center">Welcome to Computer Selling & Service Platform</h1>
        <div class="row g-4 justify-content-center mb-5">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-card text-center p-4">
                    <div class="dashboard-icon text-primary"><i class="fa-solid fa-box"></i></div>
                    <div class="dashboard-title">Total Products</div>
                    <div class="dashboard-value"><?php echo $product_count; ?></div>
                    <a href="products.php" class="dashboard-link mt-2">View Products</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-card text-center p-4">
                    <div class="dashboard-icon text-success"><i class="fa-solid fa-dollar-sign"></i></div>
                    <div class="dashboard-title">Total Sales</div>
                    <div class="dashboard-value"><?php echo $sales_count; ?></div>
                    <a href="sales.php" class="dashboard-link mt-2">View Sales</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-card text-center p-4">
                    <div class="dashboard-icon text-warning"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div class="dashboard-title">Items in Your Cart</div>
                    <div class="dashboard-value"><?php echo $cart_count; ?></div>
                    <a href="mycart.php" class="dashboard-link mt-2">Go to Cart</a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 p-4">
                    <h3 class="fw-bold mb-3">How can we help you today?</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fa-solid fa-box text-primary me-2"></i> Manage your <a href="products.php">products</a> inventory</li>
                        <li class="list-group-item"><i class="fa-solid fa-cart-shopping text-warning me-2"></i> Add products to your <a href="mycart.php">cart</a> and create invoices</li>
                        <li class="list-group-item"><i class="fa-solid fa-dollar-sign text-success me-2"></i> Track your <a href="sales.php">sales</a> and transactions</li>
                        <li class="list-group-item"><i class="fa-solid fa-user text-info me-2"></i> Update your <a href="profile.php">profile</a> and account details</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="contact.php" class="btn btn-lg btn-outline-primary px-5 py-2">Contact Us</a>
        </div>
    </div>
    <footer class="text-center py-4 bg-light mt-5">
        <small>&copy; <?php echo date('Y'); ?> Computer Selling & Service Platform</small>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>