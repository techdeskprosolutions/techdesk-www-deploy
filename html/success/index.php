<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../index.php');
    exit;
}

$full_name  = htmlspecialchars(trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')));
$username   = htmlspecialchars($_SESSION['username'] ?? '');
$privileges = htmlspecialchars($_SESSION['privileges'] ?? 'customer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Login Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body text-center p-5">
            <h1 class="display-4 mb-3">Welcome, <?= $full_name ?>!</h1>
            <p class="lead text-muted">You have successfully logged in.</p>

            <div class="my-4">
                <p><strong>Username:</strong> <?= $username ?></p>
                <p><strong>Privileges:</strong> <span class="badge bg-secondary"><?= $privileges ?></span></p>
            </div>

            <form method="post" class="d-inline">
                <button type="submit" name="logout" class="btn btn-outline-danger btn-lg px-5">Logout</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
