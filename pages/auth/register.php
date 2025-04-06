<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'birth_date' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al ve temizle
    $formData = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'phone' => preg_replace('/[^0-9]/', '', $_POST['phone']),
        'birth_date' => $_POST['birth_date']
    ];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];
    $acceptTerms = isset($_POST['accept_terms']);

    // Validasyonlar
    if (empty($formData['name'])) {
        $errors['name'] = 'Ad soyad gereklidir';
    } elseif (strlen($formData['name']) < 3) {
        $errors['name'] = 'En az 3 karakter girin';
    }

    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir email adresi girin';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Şifre en az 8 karakter olmalı';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Şifre en az 1 büyük harf ve 1 rakam içermeli';
    } elseif ($password !== $passwordConfirm) {
        $errors['password_confirm'] = 'Şifreler eşleşmiyor';
    }

    if (strlen($formData['phone']) !== 10) {
        $errors['phone'] = '10 haneli telefon numarası girin';
    }

    if (empty($formData['birth_date']) || strtotime($formData['birth_date']) > strtotime('-18 years')) {
        $errors['birth_date'] = '18 yaşından büyük olmalısınız';
    }

    if (!$acceptTerms) {
        $errors['terms'] = 'Kullanım koşullarını kabul etmelisiniz';
    }

    // Eğer hata yoksa kayıt işlemi
    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Email kontrolü
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$formData['email']]);

            if ($stmt->rowCount() > 0) {
                $errors['email'] = 'Bu email zaten kayıtlı';
            } else {
                // Kullanıcıyı kaydetme
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, birth_date) 
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $formData['name'],
                    $formData['email'],
                    $hashedPassword,
                    $formData['phone'],
                    $formData['birth_date']
                ]);

                $conn->commit();
                $_SESSION['registration_success'] = true;
                header('Location: login.php?registered=1');
                exit;
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors['general'] = 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | VivaToUR</title>
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
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-image: url('/assets/images/auth-bg.jpg');
            background-size: cover;
            background-position: center;
        }

        .auth-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary), #FF9A3E);
            color: white;
            padding: 32px;
            text-align: center;
        }

        .auth-logo {
            height: 48px;
            margin-bottom: 16px;
        }

        .auth-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .auth-content {
            padding: 32px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
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
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background-color: #F8FAFC;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            margin: 24px 0;
        }

        .checkbox-group input {
            margin-right: 12px;
            margin-top: 4px;
        }

        .checkbox-label {
            font-size: 14px;
            color: var(--dark);
            line-height: 1.5;
        }

        .checkbox-label a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .checkbox-label a:hover {
            text-decoration: underline;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #E56D00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 122, 0, 0.2);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #718096;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .password-strength {
            height: 4px;
            background: #E2E8F0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            background: #E53E3E;
            transition: all 0.3s;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .auth-container {
                border-radius: 12px;
            }

            .auth-header {
                padding: 24px;
            }

            .auth-content {
                padding: 24px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-header">
        <img src="/assets/images/logo-white.png" alt="VivaToUR" class="auth-logo">
        <h1 class="auth-title">Hesap Oluştur</h1>
        <p class="auth-subtitle">Unutulmaz deneyimler için kaydolun</p>
    </div>

    <div class="auth-content">
        <?php if(!empty($errors['general'])): ?>
            <div class="alert alert-danger" style="color: var(--danger); background: #FED7D7; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <?= $errors['general'] ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <div class="form-group <?= !empty($errors['name']) ? 'has-error' : '' ?>">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="name" class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($formData['name']) ?>" required>
                <?php if(!empty($errors['name'])): ?>
                    <span class="invalid-feedback"><?= $errors['name'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= !empty($errors['email']) ? 'has-error' : '' ?>">
                <label class="form-label">Email Adresi</label>
                <input type="email" name="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($formData['email']) ?>" required>
                <?php if(!empty($errors['email'])): ?>
                    <span class="invalid-feedback"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
                    <label class="form-label">Şifre</label>
                    <input type="password" name="password" id="password"
                           class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" required>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <?php if(!empty($errors['password'])): ?>
                        <span class="invalid-feedback"><?= $errors['password'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= !empty($errors['password_confirm']) ? 'has-error' : '' ?>">
                    <label class="form-label">Şifre Tekrar</label>
                    <input type="password" name="password_confirm"
                           class="form-control <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>" required>
                    <?php if(!empty($errors['password_confirm'])): ?>
                        <span class="invalid-feedback"><?= $errors['password_confirm'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group <?= !empty($errors['phone']) ? 'has-error' : '' ?>">
                    <label class="form-label">Telefon</label>
                    <input type="tel" name="phone" class="form-control <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($formData['phone']) ?>" placeholder="5__ ___ __ __" required>
                    <?php if(!empty($errors['phone'])): ?>
                        <span class="invalid-feedback"><?= $errors['phone'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= !empty($errors['birth_date']) ? 'has-error' : '' ?>">
                    <label class="form-label">Doğum Tarihi</label>
                    <input type="date" name="birth_date" class="form-control <?= !empty($errors['birth_date']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($formData['birth_date']) ?>" required>
                    <?php if(!empty($errors['birth_date'])): ?>
                        <span class="invalid-feedback"><?= $errors['birth_date'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="accept_terms" name="accept_terms" <?= isset($acceptTerms) && $acceptTerms ? 'checked' : '' ?>>
                <label for="accept_terms" class="checkbox-label">
                    <a href="terms.php" target="_blank">Kullanım koşullarını</a> ve
                    <a href="privacy.php" target="_blank">gizlilik politikasını</a> okudum ve kabul ediyorum
                </label>
            </div>
            <?php if(!empty($errors['terms'])): ?>
                <span class="invalid-feedback" style="display: block; margin-top: -16px; margin-bottom: 16px;"><?= $errors['terms'] ?></span>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Kayıt Ol
            </button>
        </form>

        <div class="auth-footer">
            Zaten hesabınız var mı? <a href="login.php">Giriş yapın</a>
        </div>
    </div>
</div>

<script>
    // Şifre güçlülük göstergesi
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.getElementById('strengthBar');
        let strength = 0;

        if (password.length > 0) strength += 20;
        if (password.length >= 8) strength += 30;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 20;
        if (/[^A-Za-z0-9]/.test(password)) strength += 10;

        strengthBar.style.width = strength + '%';

        if (strength < 40) {
            strengthBar.style.backgroundColor = '#E53E3E';
        } else if (strength < 70) {
            strengthBar.style.backgroundColor = '#DD6B20';
        } else {
            strengthBar.style.backgroundColor = '#38A169';
        }
    });

    // Telefon numarası formatlama
    document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = value.match(/(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            e.target.value = !value[2] ? value[1] : value[1] + ' ' + value[2] +
                (value[3] ? ' ' + value[3] : '') +
                (value[4] ? ' ' + value[4] : '');
        }
    });
</script>
</body>
</html>