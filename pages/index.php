<?php
global $conn;
require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../admin/includes/db.php';
session_start();

// Kullanıcı giriş durumunu kontrol etme
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VivaToUR - Kültür Turları</title>
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

        .hero {
            background: url('../assets/images/home-bg.jpg') no-repeat center center/cover;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 122, 0, 0.6);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: white;
            color: var(--viva-orange);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .featured-tours {
            margin-bottom: 3rem;
        }

        .section-title {
            color: var(--viva-orange);
            text-align: center;
            margin-bottom: 2rem;
        }

        .user-dashboard {
            background: var(--viva-white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 3rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .dashboard-card {
            background: var(--viva-gray);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--viva-orange);
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
    <a href="index.php" class="active"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="tours.php"><i class="fas fa-umbrella-beach"></i> Turlar</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> İletişim</a>
    <a href="about.php"><i class="fas fa-info-circle"></i> Hakkımızda</a>
    <?php if($isLoggedIn): ?>
        <a href="my-bookings.php"><i class="fas fa-suitcase"></i> Rezervasyonlarım</a>
    <?php endif; ?>
</nav>

<div class="container">
    <section class="hero">
        <div class="hero-content">
            <h1 style="font-size: 3rem; margin-bottom: 1rem;">Unutulmaz Kültür Deneyimleri</h1>
            <p style="font-size: 1.2rem; margin-bottom: 2rem;">Türkiye'nin en özel kültür turlarını keşfedin</p>
            <a href="tours.php" class="btn btn-primary">Turları Keşfet</a>
            <?php if(!$isLoggedIn): ?>
                <a href="register.php" class="btn btn-secondary">Ücretsiz Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </section>

    <?php if($isLoggedIn): ?>
        <section class="user-dashboard">
            <h2 class="section-title"><i class="fas fa-user-circle"></i> Kişisel Dashboard</h2>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3><i class="fas fa-suitcase"></i> Aktif Rezervasyonlar</h3>
                    <?php
                    $userId = $_SESSION['user_id'];
                    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND booking_date > NOW()");
                    $stmt->execute([$userId]);
                    $activeBookings = $stmt->rowCount();
                    ?>
                    <p style="font-size: 2rem; color: var(--viva-orange); margin: 1rem 0;"><?= $activeBookings ?></p>
                    <a href="my-bookings.php" class="btn btn-primary">Detayları Gör</a>
                </div>
                <div class="dashboard-card">
                    <h3><i class="fas fa-history"></i> Geçmiş Turlar</h3>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND booking_date <= NOW()");
                    $stmt->execute([$userId]);
                    $pastTours = $stmt->rowCount();
                    ?>
                    <p style="font-size: 2rem; color: var(--viva-orange); margin: 1rem 0;"><?= $pastTours ?></p>
                    <a href="past-tours.php" class="btn btn-primary">Geçmişi Gör</a>
                </div>
                <div class="dashboard-card">
                    <h3><i class="fas fa-heart"></i> Favori Turlar</h3>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM favorites WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $favorites = $stmt->rowCount();
                    ?>
                    <p style="font-size: 2rem; color: var(--viva-orange); margin: 1rem 0;"><?= $favorites ?></p>
                    <a href="favorites.php" class="btn btn-primary">Favorilerim</a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="featured-tours">
        <h2 class="section-title"><i class="fas fa-star"></i> Öne Çıkan Turlar</h2>
        <div class="tour-grid">
            <?php
            $stmt = $conn->query("SELECT * FROM tours WHERE featured = 1 LIMIT 3");
            $featuredTours = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($featuredTours as $tour):
                $image = !empty($tour['image']) ? $tour['image'] : '../assets/images/tour-default.jpg';
                ?>
                <div class="tour-card">
                    <div class="tour-image" style="background-image: url('<?= $image ?>');">
                        <span class="tour-price"><?= $tour['price'] ?> TL</span>
                    </div>
                    <div class="tour-content">
                        <h3 class="tour-title"><?= htmlspecialchars($tour['name']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($tour['short_description'])) ?></p>
                        <div class="tour-actions">
                            <a href="tour-detail.php?id=<?= $tour['id'] ?>" class="btn btn-primary">Detaylar</a>
                            <?php if($isLoggedIn): ?>
                                <button class="btn btn-secondary favorite-btn" data-tour-id="<?= $tour['id'] ?>">
                                    <i class="far fa-heart"></i> Favori
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Favori butonları
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tourId = this.dataset.tourId;
                fetch('../api/toggle_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tour_id: tourId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const icon = this.querySelector('i');
                            if(data.is_favorite) {
                                icon.classList.replace('far', 'fas');
                                this.style.backgroundColor = 'var(--viva-orange)';
                                this.style.color = 'white';
                            } else {
                                icon.classList.replace('fas', 'far');
                                this.style.backgroundColor = 'transparent';
                                this.style.color = 'var(--viva-orange)';
                            }
                        }
                    });
            });
        });
    });
</script>
</body>
</html>