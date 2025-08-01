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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .product-card {
            cursor: pointer;
            transition: box-shadow 0.2s;
            min-height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 2px solid #dee2e6; /* Add border */
        }
        .product-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .product-img {
            max-width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 0.rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="IMG/image.png" alt="TechStore Logo" height="30" class="me-2">
                TechStore
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="mycart.php">My Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Products</h2>
            <div>
                <a href="mycart.php" class="btn btn-warning me-2">Go to Cart <i class="fa fa-shopping-cart"></i></a>
                <a href="product_add.php" class="btn btn-success">Add Product</a>
            </div>
        </div>
        <div class="row g-4">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center text-muted">No products found.</div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100 border-primary position-relative">
                            <?php if ($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-img card-img-top mt-3" alt="Product Image">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title text-center mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <div class="text-center mb-2">
                                    <span class="badge bg-primary fs-6">$<?php echo number_format($product['price'], 2); ?></span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3 d-flex flex-column align-items-center">
                                <button class="btn btn-success btn-sm px-3 add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                                <button class="btn btn-link mt-2 p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#productModal<?php echo $product['id']; ?>">View Details</button>
                            </div>
                        </div>
                    </div>
                    <!-- Product Modal -->
                    <div class="modal fade" id="productModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="productModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="productModalLabel<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <?php if ($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid mb-3" alt="Product Image">
                            <?php endif; ?>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                            <p><strong>Stock:</strong> <?php echo $product['stock']; ?></p>
                            <p><strong>Created At:</strong> <?php echo $product['created_at']; ?></p>
                          </div>
                          <div class="modal-footer">
                            <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Update</a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="cartToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Product added to cart!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.getAttribute('data-product-id');
            fetch('mycart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'add_to_cart=1&product_id=' + encodeURIComponent(productId) + '&quantity=1'
            })
            .then(function(response) { return response.text(); })
            .then(function() {
                var toast = new bootstrap.Toast(document.getElementById('cartToast'));
                toast.show();
            });
        });
    });
    </script>
</body>
</html> 