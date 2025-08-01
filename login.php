<?php
session_start();
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $result = pg_query_params($conn, 'SELECT * FROM users WHERE username = $1 OR email = $1', [$username]);
    $user = pg_fetch_assoc($result);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username/email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .login-card {
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
        document.getElementById('loginForm').addEventListener('submit', checkForm);
        document.getElementById('password').addEventListener('input', validatePassword);
    });
    </script>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card login-card p-4">
                    <div class="text-center mb-4">
                        <img src="IMG/image.png" alt="TechStore Logo" height="60" class="mb-3">
                        <h2>Login to TechStore</h2>
                    </div>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="post" action="" id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username or Email</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="passwordHelp" class="form-text"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="mt-3 text-center"> 
                        <a href="register.php">Don't have an account? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>