<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
$error = '';
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    if ($product_id && $quantity > 0) {
        // Check if product exists
        $result = pg_query_params($conn, 'SELECT * FROM products WHERE id = $1', [$product_id]);
        $product = pg_fetch_assoc($result);
        if ($product) {
            // Add or update cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
        } else {
            $error = 'Product not found.';
        }
    } else {
        $error = 'Please select a product and quantity.';
    }
}

// Handle remove from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
}

// Handle update quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $pid => $qty) {
        $pid = intval($pid);
        $qty = intval($qty);
        if ($qty > 0 && isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['quantity'] = $qty;
        }
    }
}

// Handle checkout (create invoice)
$invoice = null;
$invoice_items = [];
$success = '';
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    // Create invoice
    $user_id = $_SESSION['user_id'];
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $result = pg_query_params($conn, 'INSERT INTO invoices (user_id, total, created_at) VALUES ($1, $2, NOW()) RETURNING id, created_at', [$user_id, $total]);
    $invoice = pg_fetch_assoc($result);
    $invoice_id = $invoice['id'];
    // Insert invoice items
    foreach ($_SESSION['cart'] as $item) {
        pg_query_params($conn, 'INSERT INTO invoice_items (invoice_id, product_id, product_name, price, quantity, subtotal) VALUES ($1, $2, $3, $4, $5, $6)', [
            $invoice_id,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity'],
            $item['price'] * $item['quantity']
        ]);
        $invoice_items[] = [
            'product_name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['price'] * $item['quantity']
        ];
    }
    $invoice['total'] = $total;
    $invoice['items'] = $invoice_items;
    $_SESSION['cart'] = [];
    $success = 'Invoice created successfully!';
}

// Fetch products for dropdown
$products_result = pg_query($conn, 'SELECT id, name, price FROM products ORDER BY name');
$products = [];
while ($row = pg_fetch_assoc($products_result)) {
    $products[] = $row;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Computer Selling & Service</title>
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
                    <li class="nav-item"><a class="nav-link active" href="mycart.php">My Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4">My Cart</h2>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <?php if ($invoice): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Invoice #<?php echo $invoice['id']; ?> <span class="float-end">Date: <?php echo date('Y-m-d', strtotime($invoice['created_at'])); ?></span></h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoice['items'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th>$<?php echo number_format($invoice['total'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="text-end">
                        <button class="btn btn-outline-secondary" onclick="window.print()">Print Invoice</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
        <form method="post" class="row g-3 mb-4 align-items-end">
            <div class="col-md-6">
                <label for="product_id" class="form-label">Product</label>
                <select class="form-select" name="product_id" id="product_id" required>
                    <option value="">Select product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?> ($<?php echo number_format($product['price'],2); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" id="quantity" min="1" value="1" required>
            </div>
            <div class="col-md-3">
                <button type="submit" name="add_to_cart" class="btn btn-success w-100">Add to Cart</button>
            </div>
        </form>
        <form method="post">
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-primary">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($_SESSION['cart'])): ?>
                            <tr><td colspan="5" class="text-center">Your cart is empty.</td></tr>
                        <?php else: ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <input type="number" class="form-control" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" style="width: 80px;">
                                    </td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td>
                                        <a href="mycart.php?remove=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item from cart?');">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                    <h4>Total: $<?php echo number_format($total, 2); ?></h4>
                </div>
                <button type="submit" name="checkout" class="btn btn-success w-100">Checkout & Create Invoice</button>
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 