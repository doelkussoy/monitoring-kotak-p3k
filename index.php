<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Sarana Prasarana</title>
    <link rel="icon" type="image/png" href="assets/images/cba.png">
    <meta name="description" content="Sistem Perawatan APAR - Kartu Riwayat Sarana Prasarana">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --blue: #2563eb;
            --blue-dark: #1e3a8a;
            --blue-light: #3b82f6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue) 50%, var(--blue-light) 100%);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
        }

        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .login-card {
            background: rgba(255, 255, 255, .97);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .35);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .login-top {
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
            padding: 32px 32px 24px;
            text-align: center;
            color: #fff;
        }

        .login-logo {
            height: 70px;
            object-fit: contain;
            margin-bottom: 16px;
            background: #fff;
            padding: 8px 16px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .apar-icon {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, .2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 32px;
            border: 2px solid rgba(255, 255, 255, .4);
        }

        .login-top h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .login-top p {
            font-size: 12px;
            opacity: .85;
        }

        .login-form {
            padding: 28px 32px 32px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e0e0e0;
            font-size: 14px;
            padding: 10px 14px;
        }

        .form-control:focus {
            border-color: var(--red-light);
            box-shadow: 0 0 0 3px rgba(231, 76, 60, .15);
        }

        .input-group-text {
            background: var(--blue);
            border: 1.5px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #fff;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--blue-dark), var(--blue));
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .5px;
            margin-top: 8px;
            transition: opacity .2s;
        }

        .btn-login:hover {
            opacity: .88;
            color: #fff;
        }

        footer {
            background: rgba(0, 0, 0, .25);
            color: rgba(255, 255, 255, .75);
            text-align: center;
            padding: 12px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-top">
                <img src="assets/images/cba.png" alt="Logo CBA" class="login-logo">
                <h1>SISTEM SARANA PRASARANA</h1>
                <p>Kartu Riwayat Pengecekan</p>
            </div>
            <div class="login-form">
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                            <input type="text" class="form-control" name="username" placeholder="Masukkan username"
                                required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Masukkan password"
                                required>
                        </div>
                    </div>
                    <button type="submit" name="login" class="btn-login">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>MASUK
                    </button>
                </form>
            </div>
        </div>
    </div>
    <footer>&copy; <?php echo date('Y'); ?> - Sistem Sarana Prasarana &nbsp;|&nbsp; Team IT Pabrik
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>