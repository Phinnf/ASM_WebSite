<?php
session_start();
require_once 'db.php';

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email exists
    $check = pg_query_params($conn, 'SELECT 1 FROM users WHERE username = $1 OR email = $2', [$username, $email]);
    if (pg_num_rows($check) > 0) {
        $error = 'Username or email already exists.';
    } else {
        $result = pg_query_params($conn, 'INSERT INTO users (username, email, password_hash, full_name) VALUES ($1, $2, $3, $4)', [$username, $email, $password_hash, $full_name]);
        if ($result) {
            $success = true;
            header('Location: login.php?registered=1');
            exit();
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .register-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>
    <script>
    function validatePassword() {
        const password = document.getElementById('password').value;
        const message = document.getElementById('passwordHelp');
        const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{10,}$/;
        if (!regex.test(password)) {
            message.textContent = 'Password must be at least 10 characters, include 1 uppercase letter, 1 number, and 1 special character.';
            message.style.color = 'red';
            return false;
        } else {
            message.textContent = '';
            return true;
        }
    }
    function checkForm(e) {
        if (!validatePassword()) {
            e.preventDefault();
        }
    }
    window.addEventListener('DOMContentLoaded', function() {
        document.getElementById('registerForm').addEventListener('submit', checkForm);
        document.getElementById('password').addEventListener('input', validatePassword);
    });
    </script>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card register-card p-4">
                    <h2 class="mb-4">Register</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="post" action="" id="registerForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="passwordHelp" class="form-text"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>