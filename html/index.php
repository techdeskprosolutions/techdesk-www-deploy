<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: success/index.php');
    exit;
}

$pdo = getDBConnection();
$login_error = $register_error = $success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $login_error = 'Please fill in all fields.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password, first_name, last_name, privileges FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['username']   = $user['username'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name']  = $user['last_name'];
                    $_SESSION['privileges'] = $user['privileges'];
                    header('Location: success/index.php');
                    exit;
                } else {
                    $login_error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $login_error = 'Login error. Please try again.';
            }
        }
    }

    if (isset($_POST['register'])) {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $username   = trim($_POST['reg_username'] ?? '');
        $password   = $_POST['reg_password'] ?? '';
        $confirm    = $_POST['confirm_password'] ?? '';

        if ($first_name === '' || $last_name === '' || $username === '' || $password === '' || $confirm === '') {
            $register_error = 'Please fill in all fields.';
        } elseif ($password !== $confirm) {
            $register_error = 'Passwords do not match.';
        } elseif (strlen($password) < 6) {
            $register_error = 'Password must be at least 6 characters long.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $register_error = 'Username is already taken.';
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, privileges, first_name, last_name) VALUES (?, ?, 'customer', ?, ?)");
                    $stmt->execute([$username, $hashed, $first_name, $last_name]);

                    // Auto-login after successful registration
                    $_SESSION['user_id']    = $pdo->lastInsertId();
                    $_SESSION['username']   = $username;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name']  = $last_name;
                    $_SESSION['privileges'] = 'customer';
                    header('Location: success/index.php');
                    exit;
                }
            } catch (PDOException $e) {
                $register_error = 'Registration failed. Please try again.';
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
    <title>Login / Register Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="display-5">Login / Register Test</h1>
        <p class="text-muted">Connected to MariaDB • PHP + Docker</p>
    </div>

    <div class="row g-4">
        <!-- Login Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Login</h5></div>
                <div class="card-body">
                    <?php if ($login_error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Register Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white"><h5 class="mb-0">Register (New Customer)</h5></div>
                <div class="card-body">
                    <?php if ($register_error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($register_error) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="reg_username" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="reg_password" class="form-control" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" name="register" class="btn btn-success w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4 text-muted small">
        New users are automatically assigned <strong>customer</strong> privileges.<br>
        Passwords are securely hashed with <code>password_hash()</code>.
    </div>
</div>
</body>
</html>
