<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$userId = $_SESSION['user_id'];

// Favori turları getir
$stmt = $conn->prepare("SELECT t.* FROM tours t
                       JOIN favorites f ON t.id = f.tour_id
                       WHERE f.user_id = ?");
$stmt->execute([$userId]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Favori Turlarım - VivaToUR</title>
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="user-container">
    <?php include __DIR__ . '/user-sidebar.php'; ?>

    <div class="user-content">
        <h1><i class="fas fa-heart"></i> Favori Turlarım</h1>

        <?php if(empty($favorites)): ?>
            <div class="empty-state">
                <i class="far fa-heart"></i>
                <h3>Henüz favori turunuz yok</h3>
                <p>Beğendiğiniz turları kalp ikonuna basarak favorilere ekleyebilirsiniz</p>
                <a href="/pages/tours.php" class="btn btn-orange">Turları Keşfet</a>
            </div>
        <?php else: ?>
            <div class="favorites-grid">
                <?php foreach ($favorites as $tour): ?>
                    <div class="tour-card">
                        <div class="tour-image" style="background-image: url('<?= !empty($tour['image']) ? $tour['image'] : '/assets/images/tour-default.jpg' ?>');">
                            <button class="favorite-btn active" data-tour-id="<?= $tour['id'] ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                            <span class="tour-price"><?= $tour['price'] ?> TL</span>
                        </div>
                        <div class="tour-content">
                            <h3><?= htmlspecialchars($tour['name']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($tour['short_description'])) ?></p>
                            <div class="tour-actions">
                                <a href="/pages/tour-detail.php?id=<?= $tour['id'] ?>" class="btn btn-outline">Detaylar</a>
                                <a href="/pages/booking/checkout.php?tour_id=<?= $tour['id'] ?>" class="btn btn-orange">Rezervasyon</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="/assets/js/favorites.js"></script>
</body>
</html>