
<?php
session_start();
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Demo credentials for local development only.
    // Replace with a hashed database-backed admin account before deployment.
    if ($username === 'admin' && $password === 'Admin@12345') {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = 1;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

          <style>
    body.login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 25px 15px;
        box-sizing: border-box;
    }

    .login-page .container {
        width: 100%;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .login-page .card {
        width: 100%;
        max-width: 430px;
        margin: 0 auto;
        border-radius: 12px;
    }

    .login-page .card-body {
        padding: 28px !important;
    }

    @media (max-width: 576px) {
        .login-page .card-body {
            padding: 20px !important;
        }
    }
</style>


</head>
<body class="bg-light login-page">
    <div class="container py-5">
    <div class="card shadow mx-auto p-4" style="max-width: 430px;">
        <h2 class="text-center mb-4">Admin Login</h2>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       autocomplete="username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       autocomplete="current-password" required>
            </div>

            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
