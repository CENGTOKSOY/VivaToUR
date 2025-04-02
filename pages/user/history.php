<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$userId = $_SESSION['user_id'];

// Geçmiş turları getir (tarihi geçmiş olanlar)
$stmt = $conn->prepare("SELECT b.*, t.name as tour_name, t.image, t.location 
                       FROM bookings b
                       JOIN tours t ON b.tour_id = t.id
                       WHERE b.user_id = ? AND t.date < CURDATE()
                       ORDER BY t.date DESC");
$stmt->execute([$userId]);
$pastTours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Geçmiş Turlarım - VivaToUR</title>
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="user-container">
    <?php include __DIR__ . '/user-sidebar.php'; ?>

    <div class="user-content">
        <h1><i class="fas fa-history"></i> Geçmiş Turlarım</h1>

        <?php if(empty($pastTours)): ?>
            <div class="empty-state">
                <i class="far fa-clock"></i>
                <h3>Henüz geçmiş turunuz yok</h3>
                <p>Katıldığınız turlar burada görünecektir</p>
                <a href="/pages/tours.php" class="btn btn-orange">Yeni Tur Keşfet</a>
            </div>
        <?php else: ?>
            <div class="history-list">
                <?php foreach ($pastTours as $tour): ?>
                    <div class="history-item">
                        <div class="history-image">
                            <img src="<?= !empty($tour['image']) ? $tour['image'] : '/assets/images/tour-default.jpg' ?>">
                        </div>
                        <div class="history-details">
                            <h3><?= htmlspecialchars($tour['tour_name']) ?></h3>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($tour['location']) ?></p>
                            <p><i class="fas fa-users"></i> <?= $tour['guests'] ?> kişi</p>
                            <p><i class="fas fa-calendar-day"></i> <?= date('d.m.Y', strtotime($tour['date'])) ?></p>
                        </div>
                        <div class="history-actions">
                            <button class="btn btn-outline write-review" data-tour-id="<?= $tour['tour_id'] ?>">
                                <i class="fas fa-star"></i> Yorum Yap
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Yorum Modalı -->
<div class="modal" id="reviewModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Tur Deneyiminizi Değerlendirin</h2>
        <form id="reviewForm">
            <input type="hidden" name="tour_id" id="reviewTourId">
            <div class="rating-stars">
                <!-- 5 yıldız rating sistemi -->
            </div>
            <div class="form-group">
                <label>Yorumunuz</label>
                <textarea name="review" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-orange">Gönder</button>
        </form>
    </div>
</div>

<script src="/assets/js/history.js"></script>
</body>
</html>