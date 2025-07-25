<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    pg_query_params($conn, 'DELETE FROM products WHERE id = $1', [$delete_id]);
    header('Location: products.php');
    exit();
}

// Fetch all products
$result = pg_query($conn, 'SELECT * FROM products ORDER BY id DESC');
$products = [];
while ($row = pg_fetch_assoc($result)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .product-img { max-width: 80px; max-height: 60px; object-fit: cover; }
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
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Products</h2>
            <a href="product_add.php" class="btn btn-success">Add Product</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php if ($product['image_url']): ?><img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-img" alt="Product Image"><?php endif; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['description']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td><?php echo $product['created_at']; ?></td>
                                <td>
                                    <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 