<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Favori turları getir
$stmt = $conn->prepare("SELECT t.*, 
                       TO_CHAR(t.date, 'DD.MM.YYYY') as formatted_date,
                       CASE 
                           WHEN t.type = 'cultural' THEN 'Kültürel'
                           WHEN t.type = 'festival' THEN 'Festival'
                           WHEN t.type = 'adaptation' THEN 'Adaptasyon'
                           WHEN t.type = 'historical' THEN 'Tarihi'
                           ELSE t.type
                       END as type_label
                       FROM tours t
                       JOIN favorites f ON t.id = f.tour_id
                       WHERE f.user_id = ? AND t.active = true
                       ORDER BY f.created_at DESC");
$stmt->execute([$userId]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Favori Turlarım";
include __DIR__ . '/../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - VivaToUR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF7A00;
            --primary-light: #FFE8D5;
            --primary-dark: #E56D00;
            --secondary: #333333;
            --secondary-light: #666666;
            --light: #F5F5F5;
            --white: #FFFFFF;
            --danger: #FF3B30;
            --success: #34C759;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f9f9f9;
            color: var(--secondary);
            line-height: 1.6;
        }

        /* User Dashboard Layout */
        .user-dashboard {
            display: flex;
            min-height: calc(100vh - 120px);
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 20px;
            gap: 30px;
        }

        /* Sidebar Styles */
        .user-sidebar {
            width: 280px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 2rem 1.5rem;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .user-profile {
            text-align: center;
            margin-bottom: 2rem;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }

        .user-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--secondary-light);
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            color: var(--secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .menu-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .menu-item:hover, .menu-item.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .menu-item.logout {
            color: var(--danger);
            margin-top: 1rem;
        }

        .menu-item.logout:hover {
            background-color: #fde8e8;
        }

        /* Main Content Styles */
        .user-content {
            flex: 1;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            color: var(--secondary);
        }

        .page-title i {
            color: var(--primary);
        }

        /* Tours Grid */
        .tours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .tour-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: var(--white);
            border: 1px solid #eee;
        }

        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .tour-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .tour-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .favorite-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            color: var(--primary);
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .favorite-btn:hover, .favorite-btn.active {
            background: var(--primary);
            color: white;
        }

        .tour-price {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .tour-details {
            padding: 1.5rem;
        }

        .tour-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }

        .tour-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0.8rem;
            font-size: 0.85rem;
            color: var(--secondary-light);
        }

        .tour-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .tour-meta i {
            color: var(--primary);
            font-size: 0.9rem;
        }

        .tour-description {
            color: var(--secondary-light);
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tour-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 1.4rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            flex: 1;
            gap: 8px;
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary-light);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: 2px solid var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--secondary-light);
        }

        .empty-icon {
            font-size: 3.5rem;
            color: var(--primary-light);
            margin-bottom: 1.5rem;
        }

        .empty-title {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            color: var(--secondary);
        }

        .empty-text {
            margin-bottom: 1.5rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .user-dashboard {
                flex-direction: column;
            }

            .user-sidebar {
                width: 100%;
                position: static;
            }
        }

        @media (max-width: 768px) {
            .user-content {
                padding: 1.5rem;
            }

            .tours-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="user-dashboard">
    <!-- Sidebar -->
    <aside class="user-sidebar">
        <div class="user-profile">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
            <h3 class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></h3>
            <p class="user-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
        </div>

        <nav class="sidebar-menu">
            <a href="<?= BASE_URL ?>/pages/user/profile.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Profil Bilgileri
            </a>
            <a href="<?= BASE_URL ?>/pages/user/bookings.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'bookings.php' ? 'active' : '' ?>">
                <i class="fas fa-suitcase"></i> Rezervasyonlarım
            </a>
            <a href="<?= BASE_URL ?>/pages/user/favorites.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'favorites.php' ? 'active' : '' ?>">
                <i class="fas fa-heart"></i> Favori Turlarım
            </a>
            <a href="<?= BASE_URL ?>/pages/user/history.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'history.php' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> Geçmiş Turlar
            </a>
            <a href="<?= BASE_URL ?>/pages/auth/logout.php" class="menu-item logout">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="user-content">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-heart"></i> Favori Turlarım
            </h1>
        </div>

        <?php if(empty($favorites)): ?>
            <div class="empty-state">
                <i class="far fa-heart empty-icon"></i>
                <h3 class="empty-title">Henüz favori turunuz yok</h3>
                <p class="empty-text">Beğendiğiniz turları kalp ikonuna basarak favorilerinize ekleyebilirsiniz</p>
                <a href="<?= BASE_URL ?>/pages/tours.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Turları Keşfet
                </a>
            </div>
        <?php else: ?>
            <div class="tours-grid">
                <?php foreach ($favorites as $tour): ?>
                    <?php
                    $imagePath = !empty($tour['image']) ?
                        BASE_URL . '/assets/images/tours/' . basename($tour['image']) :
                        BASE_URL . '/assets/images/tour-default.jpg';
                    ?>
                    <div class="tour-card">
                        <div class="tour-image" style="background-image: url('<?= $imagePath ?>');">
                            <span class="tour-badge"><?= $tour['type_label'] ?></span>
                            <button class="favorite-btn active" data-tour-id="<?= $tour['id'] ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                            <span class="tour-price"><?= number_format($tour['price'], 2) ?> TL</span>
                        </div>
                        <div class="tour-details">
                            <h3 class="tour-title"><?= htmlspecialchars($tour['name']) ?></h3>
                            <div class="tour-meta">
                                    <span>
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($tour['location']) ?>
                                    </span>
                                <span>
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= $tour['formatted_date'] ?>
                                    </span>
                            </div>
                            <p class="tour-description"><?= nl2br(htmlspecialchars($tour['short_description'])) ?></p>
                            <div class="tour-actions">
                                <a href="<?= BASE_URL ?>/pages/detail.php?id=<?= $tour['id'] ?>" class="btn btn-outline">
                                    <i class="fas fa-info-circle"></i> Detaylar
                                </a>
                                <a href="<?= BASE_URL ?>/pages/booking/checkout.php?tour_id=<?= $tour['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-ticket-alt"></i> Rezervasyon
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Favori butonlarına tıklama eventi
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const tourId = this.dataset.tourId;
                const isActive = this.classList.contains('active');
                const card = this.closest('.tour-card');

                // Buton durumunu değiştir
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch('<?= BASE_URL ?>/api/toggle_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tour_id: tourId,
                        action: isActive ? 'remove' : 'add'
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            if(data.action === 'removed') {
                                // Kartı kaldırma animasyonu
                                card.style.opacity = '0';
                                card.style.transform = 'scale(0.9)';
                                setTimeout(() => {
                                    card.remove();

                                    // Eğer hiç favori kalmadıysa boş durumu göster
                                    if(document.querySelectorAll('.tour-card').length === 0) {
                                        document.querySelector('.tours-grid').innerHTML = `
                                            <div class="empty-state">
                                                <i class="far fa-heart empty-icon"></i>
                                                <h3 class="empty-title">Henüz favori turunuz yok</h3>
                                                <p class="empty-text">Beğendiğiniz turları kalp ikonuna basarak favorilerinize ekleyebilirsiniz</p>
                                                <a href="<?= BASE_URL ?>/pages/tours.php" class="btn btn-primary">
                                                    <i class="fas fa-search"></i> Turları Keşfet
                                                </a>
                                            </div>
                                        `;
                                    }
                                }, 300);
                            }
                        } else {
                            // Hata durumunda butonu eski haline getir
                            this.innerHTML = '<i class="fas fa-heart"></i>';
                            if(data.message) {
                                alert(data.message);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        alert('İşlem sırasında bir hata oluştu');
                        this.innerHTML = '<i class="fas fa-heart"></i>';
                    });
            });
        });
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>