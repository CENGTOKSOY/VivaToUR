<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (empty($_GET['booking_id'])) {
    header('Location: /pages/tours.php');
    exit;
}

$bookingId = $_GET['booking_id'];
$userId = $_SESSION['user_id'];

// Rezervasyon detaylarını getir
$stmt = $conn->prepare("SELECT b.*, t.name as tour_name, t.image, t.location, t.date 
                       FROM bookings b
                       JOIN tours t ON b.tour_id = t.id
                       WHERE b.id = ? AND b.user_id = ?");
$stmt->execute([$bookingId, $userId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: /pages/tours.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rezervasyon Onayı - VivaToUR</title>
    <link rel="stylesheet" href="/assets/css/booking.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="booking-container">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <i class="fas fa-check-circle"></i>
            <h1>REZERVASYONUNUZ TAMAMLANDI!</h1>
            <p>Rezervasyon numaranız: <strong>VTR-<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?></strong></p>
        </div>

        <div class="confirmation-details">
            <div class="detail-item">
                <h3><i class="fas fa-umbrella-beach"></i> Tur Bilgileri</h3>
                <div class="tour-info">
                    <img src="<?= !empty($booking['image']) ? $booking['image'] : '/assets/images/tour-default.jpg' ?>">
                    <div>
                        <h4><?= htmlspecialchars($booking['tour_name']) ?></h4>
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($booking['location']) ?></p>
                        <p><i class="fas fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($booking['date'])) ?></p>
                        <p><i class="fas fa-users"></i> <?= $booking['guests'] ?> kişi</p>
                    </div>
                </div>
            </div>

            <div class="detail-item">
                <h3><i class="fas fa-receipt"></i> Ödeme Bilgileri</h3>
                <table class="payment-table">
                    <tr>
                        <td>Toplam Tutar:</td>
                        <td><?= number_format($booking['total_price'], 2) ?> TL</td>
                    </tr>
                    <tr>
                        <td>Ödeme Yöntemi:</td>
                        <td>Kredi Kartı</td>
                    </tr>
                    <tr>
                        <td>Rezervasyon Tarihi:</td>
                        <td><?= date('d.m.Y H:i', strtotime($booking['booking_date'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="confirmation-actions">
            <a href="/pages/user/bookings.php" class="btn btn-orange">
                <i class="fas fa-suitcase"></i> Rezervasyonlarım
            </a>
            <a href="/pages/tours.php" class="btn btn-outline">
                <i class="fas fa-umbrella-beach"></i> Yeni Tur Keşfet
            </a>
        </div>
    </div>
</div>
</body>
</html>