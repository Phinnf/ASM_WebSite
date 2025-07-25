<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = (int) $_GET['id'];
$error = '';

// Fetch product
$result = pg_query_params($conn, 'SELECT * FROM products WHERE id = $1', [$product_id]);
$product = pg_fetch_assoc($result);
if (!$product) {
    $error = 'Product not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_url = trim($_POST['image_url']);

    if ($name && $price >= 0 && $stock >= 0) {
        $result = pg_query_params($conn, 'UPDATE products SET name = $1, description = $2, price = $3, stock = $4, image_url = $5 WHERE id = $6', [$name, $description, $price, $stock, $image_url, $product_id]);
        if ($result) {
            header('Location: products.php');
            exit();
        } else {
            $error = 'Failed to update product.';
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
    <title>Edit Product - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Computer Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card p-4">
                    <h2 class="mb-4">Edit Product</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($product && !$error): ?>
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price"
                                    value="<?php echo htmlspecialchars($product['price']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock"
                                    value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="image_url" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="image_url" name="image_url"
                                    value="<?php echo htmlspecialchars($product['image_url']); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Product</button>
                            <a href="products.php" class="btn btn-link w-100 mt-2">Back to Products</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>