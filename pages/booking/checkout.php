<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (empty($_GET['tour_id'])) {
    header('Location: /pages/tours.php');
    exit;
}

$tourId = $_GET['tour_id'];
$userId = $_SESSION['user_id'];

// Tur bilgilerini getir
$stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->execute([$tourId]);
$tour = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tour) {
    header('Location: /pages/tours.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guests = $_POST['guests'];
    $cardNumber = str_replace(' ', '', $_POST['card_number']);
    $cardExpiry = explode('/', $_POST['card_expiry']);
    $cardCvc = $_POST['card_cvc'];

    // Ödeme işlemi simülasyonu
    try {
        $conn->beginTransaction();

        // Rezervasyon oluştur
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_id, guests, total_price) VALUES (?, ?, ?, ?)");
        $totalPrice = $tour['price'] * $guests;
        $stmt->execute([$userId, $tourId, $guests, $totalPrice]);
        $bookingId = $conn->lastInsertId();

        // Ödeme kaydı (simülasyon)
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, payment_method) VALUES (?, ?, 'credit_card')");
        $stmt->execute([$bookingId, $totalPrice]);

        $conn->commit();

        header("Location: confirmation.php?booking_id=$bookingId");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Ödeme işlemi başarısız: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ödeme - VivaToUR</title>
    <link rel="stylesheet" href="/assets/css/booking.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="booking-container">
    <div class="booking-steps">
        <div class="step active">1. Bilgiler</div>
        <div class="step active">2. Ödeme</div>
        <div class="step">3. Onay</div>
    </div>

    <div class="checkout-grid">
        <div class="checkout-form">
            <h2><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h2>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Misafir Sayısı</label>
                    <select name="guests" required>
                        <?php for($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> kişi</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kart Numarası</label>
                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label>Son Kullanma (AA/YY)</label>
                        <input type="text" name="card_expiry" placeholder="12/25" required>
                    </div>
                    <div class="form-group">
                        <label>CVC</label>
                        <input type="text" name="card_cvc" placeholder="123" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-orange btn-block">
                    <i class="fas fa-lock"></i> ÖDEMEYİ TAMAMLA (<?= $tour['price'] ?> TL)
                </button>
            </form>
        </div>

        <div class="booking-summary">
            <h3><i class="fas fa-receipt"></i> Tur Özeti</h3>
            <div class="tour-card">
                <img src="<?= !empty($tour['image']) ? $tour['image'] : '/assets/images/tour-default.jpg' ?>">
                <h4><?= htmlspecialchars($tour['name']) ?></h4>
                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($tour['location']) ?></p>
                <p><i class="fas fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($tour['date'])) ?></p>
                <div class="tour-price">
                    <?= number_format($tour['price'], 2) ?> TL <small>kişi başı</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>