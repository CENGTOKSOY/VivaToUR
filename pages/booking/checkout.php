<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (empty($_GET['tour_id'])) {
    header('Location: ' . BASE_URL . '/pages/tours.php');
    exit;
}

$tourId = $_GET['tour_id'];
$userId = $_SESSION['user_id'];

// Tur bilgilerini getir
$stmt = $conn->prepare("SELECT *, TO_CHAR(date, 'DD.MM.YYYY') as formatted_date FROM tours WHERE id = ?");
$stmt->execute([$tourId]);
$tour = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tour) {
    header('Location: ' . BASE_URL . '/pages/tours.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guests = (int)$_POST['guests'];
    $cardNumber = preg_replace('/\s+/', '', $_POST['card_number']);
    $cardExpiry = explode('/', $_POST['card_expiry']);
    $cardCvc = $_POST['card_cvc'];
    $cardName = trim($_POST['card_name']);

    // Validasyon
    $errors = [];

    if ($guests < 1 || $guests > 10) {
        $errors[] = "Geçersiz misafir sayısı";
    }

    if (!preg_match('/^\d{16}$/', $cardNumber)) {
        $errors[] = "Geçersiz kart numarası";
    }

    if (count($cardExpiry) != 2 || !checkdate($cardExpiry[0], 1, $cardExpiry[1])) {
        $errors[] = "Geçersiz son kullanma tarihi";
    }

    if (!preg_match('/^\d{3,4}$/', $cardCvc)) {
        $errors[] = "Geçersiz CVC kodu";
    }

    if (empty($cardName)) {
        $errors[] = "Kart sahibi adı gereklidir";
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Rezervasyon oluştur
            $totalPrice = $tour['price'] * $guests;
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_id, guests, total_price, status) VALUES (?, ?, ?, ?, 'confirmed')");
            $stmt->execute([$userId, $tourId, $guests, $totalPrice]);
            $bookingId = $conn->lastInsertId();

            // Ödeme kaydı
            $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, payment_method, card_last4) VALUES (?, ?, 'credit_card', ?)");
            $stmt->execute([$bookingId, $totalPrice, substr($cardNumber, -4)]);

            $conn->commit();

            header("Location: " . BASE_URL . "/pages/booking/confirmation.php?booking_id=$bookingId");
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Ödeme işlemi başarısız: " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme - VivaToUR</title>
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
            --border: #E0E0E0;
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

        /* Checkout Container */
        .checkout-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        /* Progress Steps */
        .checkout-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }

        .checkout-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border);
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--border);
            color: var(--secondary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .step.active .step-number {
            background: var(--primary);
            color: var(--white);
        }

        .step.completed .step-number {
            background: var(--success);
            color: var(--white);
        }

        .step.completed .step-number::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .step-label {
            font-size: 0.9rem;
            color: var(--secondary-light);
            font-weight: 500;
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        /* Checkout Grid */
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Payment Form */
        .payment-form {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }

        .section-title i {
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-group-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Card Preview */
        .card-preview {
            background: linear-gradient(135deg, #434343, #1a1a1a);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-logo {
            text-align: right;
            font-size: 1.8rem;
        }

        .card-number {
            font-family: monospace;
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin: 1rem 0;
            word-spacing: 8px;
        }

        .card-details {
            display: flex;
            justify-content: space-between;
        }

        .card-name {
            text-transform: uppercase;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .card-expiry {
            font-size: 0.9rem;
        }

        /* Summary */
        .booking-summary {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            align-self: flex-start;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }

        .summary-title i {
            color: var(--primary);
        }

        .summary-tour {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .summary-tour img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .tour-info h4 {
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }

        .tour-meta {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            font-size: 0.9rem;
            color: var(--secondary-light);
        }

        .tour-meta i {
            color: var(--primary);
            margin-right: 0.5rem;
            width: 16px;
            text-align: center;
        }

        .price-summary {
            margin-top: 1.5rem;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }

        .price-row.total {
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            color: var(--primary);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            width: 100%;
            gap: 0.8rem;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* Alert */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-danger {
            background: #fde8e8;
            color: var(--danger);
        }

        .alert i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="checkout-container">
    <!-- Progress Steps -->
    <div class="checkout-steps">
        <div class="step completed">
            <div class="step-number">1</div>
            <div class="step-label">Bilgiler</div>
        </div>
        <div class="step active">
            <div class="step-number">2</div>
            <div class="step-label">Ödeme</div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-label">Onay</div>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="checkout-grid">
        <!-- Payment Form -->
        <div class="payment-form">
            <h2 class="section-title">
                <i class="fas fa-credit-card"></i> Ödeme Bilgileri
            </h2>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>

            <!-- Card Preview -->
            <div class="card-preview">
                <div class="card-logo">
                    <i class="fab fa-cc-visa"></i>
                </div>
                <div class="card-number" id="cardPreview">•••• •••• •••• ••••</div>
                <div class="card-details">
                    <div class="card-name" id="cardNamePreview">KART SAHİBİ</div>
                    <div class="card-expiry" id="cardExpiryPreview">••/••</div>
                </div>
            </div>

            <form method="POST" id="paymentForm">
                <div class="form-group">
                    <label for="guests">Misafir Sayısı</label>
                    <select name="guests" id="guests" required>
                        <?php for($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>" <?= isset($_POST['guests']) && $_POST['guests'] == $i ? 'selected' : '' ?>>
                                <?= $i ?> kişi
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="card_name">Kart Sahibi Adı</label>
                    <input type="text" name="card_name" id="card_name" placeholder="Ad Soyad"
                           value="<?= isset($_POST['card_name']) ? htmlspecialchars($_POST['card_name']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="card_number">Kart Numarası</label>
                    <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456"
                           value="<?= isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : '' ?>" required>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label for="card_expiry">Son Kullanma (AA/YY)</label>
                        <input type="text" name="card_expiry" id="card_expiry" placeholder="12/25"
                               value="<?= isset($_POST['card_expiry']) ? htmlspecialchars($_POST['card_expiry']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="card_cvc">CVC</label>
                        <input type="text" name="card_cvc" id="card_cvc" placeholder="123"
                               value="<?= isset($_POST['card_cvc']) ? htmlspecialchars($_POST['card_cvc']) : '' ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-lock"></i> ÖDEMEYİ TAMAMLA (<?= number_format($tour['price'], 2) ?> TL)
                </button>
            </form>
        </div>

        <!-- Booking Summary -->
        <div class="booking-summary">
            <h3 class="summary-title">
                <i class="fas fa-receipt"></i> Tur Özeti
            </h3>

            <div class="summary-tour">
                <img src="<?= !empty($tour['image']) ? BASE_URL . '/assets/images/tours/' . basename($tour['image']) : BASE_URL . '/assets/images/tour-default.jpg' ?>"
                     alt="<?= htmlspecialchars($tour['name']) ?>">
                <div class="tour-info">
                    <h4><?= htmlspecialchars($tour['name']) ?></h4>
                    <div class="tour-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($tour['location']) ?></span>
                        <span><i class="fas fa-calendar-alt"></i> <?= $tour['formatted_date'] ?></span>
                    </div>
                </div>
            </div>

            <div class="price-summary">
                <div class="price-row">
                    <span>Fiyat</span>
                    <span><?= number_format($tour['price'], 2) ?> TL</span>
                </div>
                <div class="price-row">
                    <span>Misafir Sayısı</span>
                    <span id="summaryGuests">1 kişi</span>
                </div>
                <div class="price-row total">
                    <span>Toplam</span>
                    <span id="summaryTotal"><?= number_format($tour['price'], 2) ?> TL</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kart önizleme güncelleme
        const cardNumber = document.getElementById('card_number');
        const cardName = document.getElementById('card_name');
        const cardExpiry = document.getElementById('card_expiry');
        const guestsSelect = document.getElementById('guests');

        // Misafir sayısı değişikliğinde toplam fiyatı güncelle
        guestsSelect.addEventListener('change', updateTotalPrice);

        // Kart bilgileri için gerçek zamanlı önizleme
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            if (value.length > 0) {
                value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
            }
            e.target.value = value;
            document.getElementById('cardPreview').textContent = value || '•••• •••• •••• ••••';
        });

        cardName.addEventListener('input', function(e) {
            document.getElementById('cardNamePreview').textContent = e.target.value.toUpperCase() || 'KART SAHİBİ';
        });

        cardExpiry.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
            document.getElementById('cardExpiryPreview').textContent = value || '••/••';
        });

        // Form gönderiminde kart numarasındaki boşlukları temizle
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            cardNumber.value = cardNumber.value.replace(/\s/g, '');
        });

        // Toplam fiyatı güncelle
        function updateTotalPrice() {
            const guests = parseInt(guestsSelect.value);
            const pricePerPerson = <?= $tour['price'] ?>;
            const total = guests * pricePerPerson;

            document.getElementById('summaryGuests').textContent = guests + ' kişi';
            document.getElementById('summaryTotal').textContent = total.toFixed(2) + ' TL';

            // Buton metnini güncelle
            document.querySelector('button[type="submit"]').innerHTML =
                `<i class="fas fa-lock"></i> ÖDEMEYİ TAMAMLA (${total.toFixed(2)} TL)`;
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>