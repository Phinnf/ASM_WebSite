<?php
session_start();
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app, you would send an email or store the message
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .contact-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card contact-card p-4">
                    <h2 class="mb-4">Contact Us</h2>
                    <?php if ($success): ?>
                        <div class="alert alert-success">Thank you for contacting us! We will get back to you soon.</div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                    <hr>
                    <div class="mt-3">
                        <h5>Company Information</h5>
                        <p class="mb-1">Computer Selling & Service Platform</p>
                        <p class="mb-1">66 Võ Văn Tần, Đà Nẵng</p>
                        <p class="mb-1">Phone: (123) 456-7890</p>
                        <p>Email: info@computerstore.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 bg-light mt-5">
        <small>&copy; <?php echo date('Y'); ?> Computer Selling & Service Platform</small>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>