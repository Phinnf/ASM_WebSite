<?php
// Start session for user authentication (future use)
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Selling & Service - Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .dashboard-card {
            transition: box-shadow 0.2s;
        }

        .dashboard-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.10);
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
                    <li class="nav-item"><a class="nav-link" href="#">Customers</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="#">Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
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
        <h1 class="mb-4">Welcome to Computer Selling & Service Platform</h1>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Customers</h5>
                        <p class="display-6">--</p>
                        <a href="#" class="btn btn-outline-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="display-6">--</p>
                        <a href="#" class="btn btn-outline-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Sales</h5>
                        <p class="display-6">--</p>
                        <a href="#" class="btn btn-outline-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Services</h5>
                        <p class="display-6">--</p>
                        <a href="#" class="btn btn-outline-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Inventory</h5>
                        <p class="display-6">--</p>
                        <a href="#" class="btn btn-outline-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="display-6">--</p>
                        <a href="#" class="btn btn-outline-primary">Manage</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="contact.php" class="btn btn-lg btn-success">Contact Us</a>
        </div>
    </div>

    <footer class="text-center py-4 bg-light mt-5">
        <small>&copy; <?php echo date('Y'); ?> Computer Selling & Service Platform</small>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>