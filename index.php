<?php
include "db.php";
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND pass = '$password'");

    if (mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
        $_SESSION["username"] = $username;
        $_SESSION["id"] = $userData["id"];
        $_SESSION["role"] = $userData["role"];
        $_SESSION["nama"] = $userData["nama"];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MONITORING KOTAK P3K</title>
    <link rel="shortcut icon" href="assets/images/cba-text.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            animation: fadeIn 0.6s ease-out;
        }

        .login-logo {
            background: var(--primary);
            width: 64px;
            height: 64px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
            color: white;
        }

        .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: var(--primary);
        }
    </style>
</head>

<body>
    <div class="login-card glass-card">
        <div class="text-center mb-4">
            <img src="assets/images/cba-text.png" alt="Logo CBA" class="mb-3" style="height: 60px; width: auto;">
            <h3 class="mb-1">Monitoring Kotak P3K</h3>
            <p class="text-secondary small">Digital Monitoring System</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger border-0 small py-2 mb-3" style="border-radius: 0.5rem;">
                <i data-lucide="alert-circle" style="width:16px; height:16px; vertical-align:middle;" class="me-1"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required
                    autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn-premium w-100 justify-content-center">
                Masuk ke Dashboard
                <i data-lucide="arrow-right" style="width:18px; height:18px;"></i>
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-secondary tiny mb-0" style="font-size: 0.75rem;">
                &copy; <?= date('Y') ?> Monitoring Kotak P3K | Team IT Pabrik
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>