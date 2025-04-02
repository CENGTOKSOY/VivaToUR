<?php
global $conn;
require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../admin/includes/db.php';
session_start();

// Kullanıcı giriş durumunu kontrol etmek için
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Giriş yapmış kullanıcı için otomatik doldurma
    if ($isLoggedIn && empty($name) && empty($email)) {
        $name = $_SESSION['user_name'];
        $email = $_SESSION['user_email'];
    }

    // Form doğrulama
    $errors = [];
    if (empty($name)) $errors[] = 'Adınızı giriniz';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli bir email giriniz';
    if (empty($message)) $errors[] = 'Mesajınızı giriniz';

    if (empty($errors)) {
        // Burada mesajı veritabanına kaydedebilir veya email gönderebiliriz
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VivaToUR - İletişim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --viva-orange: #FF7A00;
            --viva-orange-light: #FFE8D5;
            --viva-dark: #333333;
            --viva-gray: #F5F5F5;
            --viva-white: #FFFFFF;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: var(--viva-gray);
            color: var(--viva-dark);
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--viva-orange), #FF9A3E);
            color: white;
            padding: 1rem 0;
            position: relative;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--viva-orange);
            font-weight: bold;
        }

        nav {
            background-color: var(--viva-white);
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav a {
            color: var(--viva-dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }

        nav a:hover, nav a.active {
            background-color: var(--viva-orange-light);
            color: var(--viva-orange);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .contact-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .contact-info {
            background: var(--viva-white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .contact-info h2 {
            color: var(--viva-orange);
            margin-top: 0;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .info-icon {
            background: var(--viva-orange-light);
            color: var(--viva-orange);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .contact-form {
            background: var(--viva-white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1rem;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--viva-orange);
            color: white;
        }

        .btn-primary:hover {
            background: #e06d00;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }

        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        footer {
            background: var(--viva-dark);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
<header>
    <div class="header-content">
        <div class="logo">VivaToUR</div>
        <div class="user-menu">
            <?php if($isLoggedIn): ?>
                <span>Hoş geldiniz, <?= htmlspecialchars($userName) ?></span>
                <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <a href="profile.php" style="color: white;"><i class="fas fa-user"></i></a>
                <a href="logout.php" style="color: white;"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Giriş Yap</a>
                <a href="auth/register.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="tours.php"><i class="fas fa-umbrella-beach"></i> Turlar</a>
    <a href="contact.php" class="active"><i class="fas fa-envelope"></i> İletişim</a>
    <a href="about.php"><i class="fas fa-info-circle"></i> Hakkımızda</a>
    <?php if($isLoggedIn): ?>
        <a href="my-bookings.php"><i class="fas fa-suitcase"></i> Rezervasyonlarım</a>
    <?php endif; ?>
</nav>

<div class="container">
    <?php if(isset($success) && $success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Mesajınız başarıyla gönderildi. En kısa sürede sizinle iletişime geçeceğiz.
        </div>
    <?php elseif(!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= implode('<br>', $errors) ?>
        </div>
    <?php endif; ?>

    <h1 style="color: var(--viva-orange); text-align: center; margin-bottom: 2rem;">Bizimle İletişime Geçin</h1>

    <div class="contact-section">
        <div class="contact-info">
            <h2><i class="fas fa-info-circle"></i> İletişim Bilgileri</h2>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h3>Adres</h3>
                    <p>Altunizade Mah. Kısıklı Cad. No:123<br>Üsküdar/İstanbul</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                    <h3>Telefon</h3>
                    <p><a href="tel:+902165541234">+90 216 554 12 34</a></p>
                    <p><a href="tel:+905551234567">+90 555 123 45 67</a> (WhatsApp)</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h3>Email</h3>
                    <p><a href="mailto:info@vivatour.com">info@vivatour.com</a></p>
                    <p><a href="mailto:rezervasyon@vivatour.com">rezervasyon@vivatour.com</a></p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3>Çalışma Saatleri</h3>
                    <p>Pazartesi - Cuma: 09:00 - 18:00</p>
                    <p>Cumartesi: 10:00 - 15:00</p>
                    <p>Pazar: Kapalı</p>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h2><i class="fas fa-paper-plane"></i> Mesaj Gönder</h2>
            <form method="POST" action="contact.php">
                <div class="form-group">
                    <label for="name">Adınız Soyadınız</label>
                    <input type="text" id="name" name="name" required
                           value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Adresiniz</label>
                    <input type="email" id="email" name="email" required
                           value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="subject">Konu</label>
                    <select id="subject" name="subject" required>
                        <option value="">Konu Seçiniz</option>
                        <option value="tour">Tur Bilgisi</option>
                        <option value="booking">Rezervasyon</option>
                        <option value="cancel">İptal/Değişiklik</option>
                        <option value="complaint">Şikayet</option>
                        <option value="other">Diğer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Mesajınız</label>
                    <textarea id="message" name="message" required><?= isset($message) ? htmlspecialchars($message) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Mesajı Gönder
                </button>
            </form>
        </div>
    </div>

    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.279637145691!2d29.03458231541185!3d41.0228269792995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cac826d524c9f1%3A0xc14f7612337b7f38!2sAltunizade%2C%20K%C4%B1s%C4%B1kl%C4%B1%20Cd.%20No%3A123%2C%2034662%20%C3%9Csk%C3%BCdar%2F%C4%B0stanbul!5e0!3m2!1str!2str!4v1623256789012!5m2!1str!2str"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<footer>
    <p>&copy; 2025 VivaToUR - Tüm Hakları Saklıdır</p>
    <div style="margin-top: 1rem;">
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-facebook"></i></a>
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-instagram"></i></a>
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-whatsapp"></i></a>
    </div>
</footer>

<script src="../assets/js/script.js"></script>
</body>
</html>