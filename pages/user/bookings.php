<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT b.*, t.name as tour_name, t.image 
                       FROM bookings b
                       JOIN tours t ON b.tour_id = t.id
                       WHERE b.user_id = ? ORDER BY b.booking_date DESC");
$stmt->execute([$userId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rezervasyonlarım - VivaToUR</title>
    <!-- Ortak stil dosyaları -->
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container">
    <h1>Rezervasyonlarım</h1>

    <div class="booking-list">
        <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <img src="<?= htmlspecialchars($booking['image']) ?>">
                <h3><?= htmlspecialchars($booking['tour_name']) ?></h3>
                <p>Tarih: <?= date('d.m.Y', strtotime($booking['date'])) ?></p>
                <!-- Diğer detaylar -->
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>