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
    pg_query_params($conn, 'DELETE FROM sales WHERE id = $1', [$delete_id]);
    header('Location: sales.php');
    exit();
}

// Handle add
$error = '';
if (isset($_POST['add_sale'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $customer_name = trim($_POST['customer_name']);
    $sale_date = $_POST['sale_date'];
    // Get product price
    $result = pg_query_params($conn, 'SELECT price FROM products WHERE id = $1', [$product_id]);
    $product = pg_fetch_assoc($result);
    if ($product && $quantity > 0 && $customer_name) {
        $total_price = $product['price'] * $quantity;
        $res = pg_query_params($conn, 'INSERT INTO sales (product_id, quantity, total_price, sale_date, customer_name) VALUES ($1, $2, $3, $4, $5)', [$product_id, $quantity, $total_price, $sale_date, $customer_name]);
        if ($res) {
            header('Location: sales.php');
            exit();
        } else {
            $error = 'Failed to add sale.';
        }
    } else {
        $error = 'Please fill all required fields correctly.';
    }
}

// Fetch all sales
$sales_result = pg_query($conn, 'SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON s.product_id = p.id ORDER BY s.id DESC');
$sales = [];
while ($row = pg_fetch_assoc($sales_result)) {
    $sales[] = $row;
}
// Fetch products for dropdown
$products_result = pg_query($conn, 'SELECT id, name FROM products ORDER BY name');
$products = [];
while ($row = pg_fetch_assoc($products_result)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
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
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="sales.php">Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sales Transactions</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSaleModal">Add Sale</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Sale Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                        <tr><td colspan="7" class="text-center">No sales found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo $sale['id']; ?></td>
                                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                <td><?php echo $sale['quantity']; ?></td>
                                <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                                <td><?php echo $sale['sale_date']; ?></td>
                                <td>
                                    <a href="sales_edit.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="sales.php?delete=<?php echo $sale['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this sale?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Sale Modal -->
    <div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="addSaleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post">
            <div class="modal-header">
              <h5 class="modal-title" id="addSaleModalLabel">Add Sale</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
              <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select class="form-select" name="product_id" id="product_id" required>
                  <option value="">Select product</option>
                  <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" name="customer_name" id="customer_name" required>
              </div>
              <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
              </div>
              <div class="mb-3">
                <label for="sale_date" class="form-label">Sale Date</label>
                <input type="date" class="form-control" name="sale_date" id="sale_date" required value="<?php echo date('Y-m-d'); ?>">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="add_sale" class="btn btn-success">Add Sale</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 