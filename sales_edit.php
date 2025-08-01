<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: sales.php');
    exit();
}

$sale_id = (int) $_GET['id'];
$error = '';

// Fetch sale
$result = pg_query_params($conn, 'SELECT * FROM sales WHERE id = $1', [$sale_id]);
$sale = pg_fetch_assoc($result);
if (!$sale) {
    $error = 'Sale not found.';
}

// Fetch products for dropdown
$products_result = pg_query($conn, 'SELECT id, name FROM products ORDER BY name');
$products = [];
while ($row = pg_fetch_assoc($products_result)) {
    $products[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $customer_name = trim($_POST['customer_name']);
    $sale_date = $_POST['sale_date'];
    // Get product price
    $result = pg_query_params($conn, 'SELECT price FROM products WHERE id = $1', [$product_id]);
    $product = pg_fetch_assoc($result);
    if ($product && $quantity > 0 && $customer_name) {
        $total_price = $product['price'] * $quantity;
        $res = pg_query_params($conn, 'UPDATE sales SET product_id = $1, quantity = $2, total_price = $3, sale_date = $4, customer_name = $5 WHERE id = $6', [$product_id, $quantity, $total_price, $sale_date, $customer_name, $sale_id]);
        if ($res) {
            header('Location: sales.php');
            exit();
        } else {
            $error = 'Failed to update sale.';
        }
    } else {
        $error = 'Please fill all required fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sale - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Computer Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="sales.php">Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Edit Sale</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-select" name="product_id" id="product_id" required>
                                    <option value="">Select product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" <?php if ($sale['product_id'] == $product['id']) echo 'selected'; ?>><?php echo htmlspecialchars($product['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo htmlspecialchars($sale['customer_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="quantity" id="quantity" min="1" value="<?php echo $sale['quantity']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="sale_date" class="form-label">Sale Date</label>
                                <input type="date" class="form-control" name="sale_date" id="sale_date" value="<?php echo $sale['sale_date']; ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="sales.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Sale</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 