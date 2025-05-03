<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

// Oturumu başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Kullanıcı oturum bilgilerini ayarla
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['last_login'] = time();
            $_SESSION['logged_in'] = true;

            // Son giriş tarihini güncelle
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Başarılı giriş mesajı
            $_SESSION['login_success'] = true;

            // Yönlendirme
            header('Location: /pages/user/profile.php');
            exit;
        } else {
            $error = "Girdiğiniz bilgilerle eşleşen bir hesap bulunamadı";
        }
    } catch (PDOException $e) {
        error_log("Giriş hatası: " . $e->getMessage());
        $error = "Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | VivaToUR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF7A00;
            --primary-light: #FFE8D5;
            --dark: #2D3748;
            --light: #F7FAFC;
            --danger: #E53E3E;
            --success: #38A169;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-image: url('/assets/images/auth-bg1.jpg');
            background-size: cover;
            background-position: center;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            animation: fadeIn 0.6s ease;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary), #FF9A3E);
            color: white;
            padding: 40px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/assets/images/pattern.png') repeat;
            opacity: 0.1;
        }

        .auth-logo {
            height: 48px;
            margin-bottom: 16px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .auth-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .auth-subtitle {
            font-size: 15px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .auth-content {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #F8FAFC;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #E56D00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 122, 0, 0.25);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .auth-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            color: #718096;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin: 0 6px;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #A0AEC0;
            cursor: pointer;
        }

        .password-container {
            position: relative;
        }

        .alert {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .alert-danger {
            color: var(--danger);
            background: #FED7D7;
            border-color: var(--danger);
        }

        .alert-success {
            color: var(--success);
            background: #C6F6D5;
            border-color: var(--success);
        }

        /* Responsive */
        @media (max-width: 576px) {
            .auth-container {
                border-radius: 12px;
            }

            .auth-header {
                padding: 32px 24px;
            }

            .auth-content {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-header">
        <img src="/assets/images/logo-white.png" alt="VivaToUR" class="auth-logo">
        <h1 class="auth-title">Hesabınıza Giriş Yapın</h1>
        <p class="auth-subtitle">Kültür turlarının keyfini çıkarın</p>
    </div>

    <div class="auth-content">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['registered'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Kayıt işlemi başarılı! Giriş yapabilirsiniz
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label class="form-label">Email Adresiniz</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($email) ?>"
                       placeholder="ornek@email.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Şifreniz</label>
                <div class="password-container">
                    <input type="password" name="password" id="password"
                           class="form-control" placeholder="••••••••" required>
                    <span class="password-toggle" id="togglePassword">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
                <div style="text-align: right; margin-top: 8px;">
                    <a href="forgot-password.php" style="color: var(--primary); font-size: 13px; text-decoration: none;">
                        Şifremi Unuttum?
                    </a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>

        <div class="auth-footer">
            Hesabınız yok mu? <a href="register.php">Kayıt Olun</a>
        </div>
    </div>
</div>

<script>
    // Şifre göster/gizle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Form gönderim animasyonu
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Giriş Yapılıyor...';
        btn.disabled = true;
    });
</script>
</body>
</html>