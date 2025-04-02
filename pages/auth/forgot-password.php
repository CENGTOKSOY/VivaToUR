<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Token oluştur ve veritabanına kaydet
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    try {
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        // E-posta gönderim fonksiyonu (simülasyon)
        $resetLink = SITE_URL . "/pages/auth/reset-password.php?token=$token";
        // mail($email, "Şifre Sıfırlama", "Link: $resetLink");

        $success = "Şifre sıfırlama linki e-posta adresinize gönderildi!";
    } catch (PDOException $e) {
        $error = "Bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Şifremi Unuttum - VivaToUR</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
<div class="auth-container">
    <h1><i class="fas fa-key"></i> Şifremi Unuttum</h1>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Kayıtlı E-posta Adresiniz</label>
            <input type="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-orange">Şifre Sıfırlama Linki Gönder</button>
    </form>
    <div class="auth-links">
        <a href="login.php"><i class="fas fa-arrow-left"></i> Giriş Sayfasına Dön</a>
    </div>
</div>
</body>
</html>