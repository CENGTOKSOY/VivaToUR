<?php
// admin/login.php
session_start();

// Hata mesajı
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];


    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Kullanıcı adı veya şifre hatalı!';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VivaToUR - Admin Giriş</title>
    <style>
        :root {
            --viva-orange: #FF7A00;
            --viva-dark: #333;
            --viva-light: #FFF8F0;
        }

        body {
            background-color: var(--viva-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('../assets/images/bg-pattern.png');
            background-size: cover;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 122, 0, 0.2);
            width: 400px;
            padding: 40px;
            text-align: center;
            border-top: 5px solid var(--viva-orange);
        }

        .logo {
            width: 180px;
            margin-bottom: 30px;
        }

        h1 {
            color: var(--viva-orange);
            margin-bottom: 30px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--viva-dark);
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus {
            border-color: var(--viva-orange);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
        }

        .btn-login {
            background-color: var(--viva-orange);
            color: white;
            border: none;
            padding: 14px 20px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #E56D00;
        }

        .error-message {
            color: #d9534f;
            background-color: #fdf2f2;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: <?= $error ? 'block' : 'none' ?>;
        }

        .forgot-password {
            display: block;
            margin-top: 20px;
            color: var(--viva-orange);
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-container">
    <!-- Logo eklemek için (assets/images/logo.png yolunu kullan) -->
    <img src="../assets/images/logo.png" alt="VivaToUR Logo" class="logo">

    <h1>Admin Paneli Giriş</h1>

    <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label for="username">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" placeholder="admin" required>
        </div>

        <div class="input-group">
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">Giriş Yap</button>

        <a href="#" class="forgot-password">Şifremi Unuttum?</a>
    </form>
</div>
</body>
</html>