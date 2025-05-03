<?php
global $conn;
require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../includes/db.php';
session_start();

// Kullanıcı giriş durumunu kontrol etme
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// URL'den tur ID'sini al
$tourId = $_GET['id'] ?? 0;

// Tur detaylarını veritabanından çek
try {
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$tourId]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tour) {
        header('Location: tours.php');
        exit;
    }

    // Görsel yolunu hazırla
    $imagePath = '../assets/images/tour-default.jpg';
    if (!empty($tour['image'])) {
        $imageParts = explode('_', $tour['image']);
        $imageFile = end($imageParts);
        $potentialPath = '../assets/images/tours/' . $imageFile;
        if (file_exists($potentialPath)) {
            $imagePath = $potentialPath;
        }
    }

} catch (PDOException $e) {
    error_log("Tur detayları çekilirken hata: " . $e->getMessage());
    header('Location: tours.php');
    exit;
}

// Tur türleri için etiketler
$typeLabels = [
    'cultural' => 'Kültürel Tur',
    'festival' => 'Festival Turu',
    'adaptation' => 'Adaptasyon Turu',
    'historical' => 'Tarihi Tur'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tour['name']) ?> | VivaToUR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --viva-orange: #FF7A00;
            --viva-orange-light: #FFE8D5;
            --viva-dark: #333333;
            --viva-gray: #F5F5F5;
            --viva-white: #FFFFFF;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: var(--viva-dark);
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--viva-orange), #FF9A3E);
            color: white;
            padding: 1rem 0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
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
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav a {
            color: var(--viva-dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Tur Detay Stilleri */
        .tour-header {
            display: flex;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .tour-image-container {
            flex: 1;
            min-height: 400px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .tour-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-basic-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .tour-title {
            font-size: 2.5rem;
            color: var(--viva-dark);
            margin-bottom: 1rem;
        }

        .tour-meta {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            color: #666;
        }

        .meta-item i {
            color: var(--viva-orange);
        }

        .price-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            text-align: center;
        }

        .price-label {
            font-size: 1rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .price-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--viva-orange);
        }

        /* Detail Cards */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .detail-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        .card-title {
            font-size: 1.5rem;
            color: var(--viva-orange);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .description-text {
            line-height: 1.8;
            color: #555;
        }

        /* Action Button */
        .action-container {
            text-align: center;
            margin: 3rem 0;
        }

        .btn-book {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--viva-orange);
            color: white;
            padding: 1.25rem 2.5rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 122, 0, 0.3);
        }

        .btn-book:hover {
            background: #e66d00;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 122, 0, 0.4);
        }

        footer {
            background: var(--viva-dark);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .social-links {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            transition: transform 0.3s;
        }

        .social-links a:hover {
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .tour-header {
                flex-direction: column;
            }

            .tour-image-container {
                min-height: 300px;
            }

            .tour-title {
                font-size: 2rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <a href="index.php" class="logo">VivaToUR</a>
        <div class="user-menu">
            <?php if($isLoggedIn): ?>
                <span>Hoş geldiniz, <?= htmlspecialchars($userName) ?></span>
                <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <a href="user/profile.php" style="color: white;"><i class="fas fa-user"></i></a>
                <a href="auth/logout.php" style="color: white;"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Giriş Yap</a>
                <a href="auth/register.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="tours.php" class="active"><i class="fas fa-umbrella-beach"></i> Turlar</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> İletişim</a>
    <a href="about.php"><i class="fas fa-info-circle"></i> Hakkımızda</a>
    <?php if($isLoggedIn): ?>
        <a href="user/bookings.php"><i class="fas fa-suitcase"></i> Rezervasyonlarım</a>
    <?php endif; ?>
</nav>

<div class="container">
    <section class="tour-header">
        <div class="tour-image-container">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($tour['name']) ?>" class="tour-image">
        </div>

        <div class="tour-basic-info">
            <div>
                <h1 class="tour-title"><?= htmlspecialchars($tour['name']) ?></h1>

                <div class="tour-meta">
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($tour['location']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= date('d.m.Y', strtotime($tour['date'])) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span><?= $typeLabels[$tour['type']] ?? $tour['type'] ?></span>
                    </div>
                </div>

                <p><?= htmlspecialchars($tour['short_description']) ?></p>
            </div>

            <div class="price-container">
                <div class="price-label">Kişi Başı</div>
                <div class="price-value"><?= number_format($tour['price'], 2) ?> TL</div>
            </div>
        </div>
    </section>

    <section class="detail-grid">
        <div class="detail-card">
            <h2 class="card-title">
                <i class="fas fa-info-circle"></i>
                Tur Detayları
            </h2>
            <p class="description-text"><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
        </div>

        <div class="detail-card">
            <h2 class="card-title">
                <i class="fas fa-map-marked-alt"></i>
                Lokasyon Bilgileri
            </h2>
            <div class="meta-item" style="margin-bottom: 1rem;">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars($tour['location']) ?></span>
            </div>
            <p class="description-text">
                Turumuz <?= htmlspecialchars($tour['location']) ?> bölgesinde gerçekleşecektir.
                Detaylı buluşma noktası bilgileri rezervasyon sonrasında tarafınıza iletilecektir.
            </p>
        </div>

        <div class="detail-card">
            <h2 class="card-title">
                <i class="fas fa-calendar-day"></i>
                Tur Programı
            </h2>
            <p class="description-text">
                Tur tarihi: <?= date('d.m.Y', strtotime($tour['date'])) ?><br><br>
                Tur program detayları rehberimiz tarafından tur günü katılımcılarla paylaşılacaktır.
            </p>
        </div>

        <div class="detail-card">
            <h2 class="card-title">
                <i class="fas fa-clipboard-check"></i>
                Dahil Olanlar
            </h2>
            <ul style="list-style-type: none; color: #555; line-height: 2;">
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Profesyonel rehberlik</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Ulaşım</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Öğle yemeği</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Müze giriş ücretleri</li>
            </ul>
        </div>

        <div class="detail-card">
            <h2 class="card-title">
                <i class="fas fa-clipboard-check"></i>
                Dahil Olanlar
            </h2>
            <ul style="list-style-type: none; color: #555; line-height: 2;">
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Profesyonel rehberlik</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Ulaşım</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Öğle yemeği</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Müze giriş ücretleri</li>
            </ul>
        </div>

        <div class="detail-card">
            <h2 class="card-title">
                <i class="fas fa-clipboard-check"></i>
                Dahil Olanlar
            </h2>
            <ul style="list-style-type: none; color: #555; line-height: 2;">
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Profesyonel rehberlik</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Ulaşım</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Öğle yemeği</li>
                <li><i class="fas fa-check-circle" style="color: var(--viva-orange);"></i> Müze giriş ücretleri</li>
            </ul>
        </div>

    </section>

    <div class="action-container">
        <?php if($isLoggedIn): ?>
            <a href="booking.php?tour_id=<?= $tour['id'] ?>" class="btn-book">
                <i class="fas fa-ticket-alt"></i>
                Hemen Rezervasyon Yap
            </a>
        <?php else: ?>
            <a href="auth/login.php" class="btn-book">
                <i class="fas fa-sign-in-alt"></i>
                Rezervasyon İçin Giriş Yapın
            </a>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 VivaToUR - Tüm Hakları Saklıdır</p>
    <div class="social-links">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-whatsapp"></i></a>
    </div>
</footer>
</body>
</html>