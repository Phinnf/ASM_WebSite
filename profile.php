<?php
session_start();
require_once 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user info
$result = pg_query_params($conn, 'SELECT username, email, full_name, password_hash FROM users WHERE id = $1', [$user_id]);
$user = pg_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $update_password = false;

    // Check if password change is requested
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        if (password_verify($_POST['current_password'], $user['password_hash'])) {
            $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $update_password = true;
        } else {
            $error = 'Current password is incorrect.';
        }
    }

    if (!$error) {
        // Check for username/email conflicts (excluding self)
        $check = pg_query_params($conn, 'SELECT 1 FROM users WHERE (username = $1 OR email = $2) AND id != $3', [$username, $email, $user_id]);
        if (pg_num_rows($check) > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Update user info
            $query = 'UPDATE users SET username = $1, email = $2, full_name = $3' . ($update_password ? ', password_hash = $4' : '') . ' WHERE id = $5';
            $params = [$username, $email, $full_name];
            if ($update_password) {
                $params[] = $new_password_hash;
            }
            $params[] = $user_id;
            $result = pg_query_params($conn, $query, $params);
            if ($result) {
                $success = 'Profile updated successfully.';
                // Refresh user info
                $result = pg_query_params($conn, 'SELECT username, email, full_name, password_hash FROM users WHERE id = $1', [$user_id]);
                $user = pg_fetch_assoc($result);
                $_SESSION['username'] = $user['username'];
            } else {
                $error = 'Failed to update profile.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Computer Selling & Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .profile-card {
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
                    <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card profile-card p-4">
                    <h2 class="mb-4">My Profile</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <hr>
                        <h5>Change Password</h5>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>