<?php
// pages/user/history.php

global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/auth/login.php');
    exit;
}

// PostgreSQL uyumlu tarih sorgusu
$stmt = $conn->prepare("
    SELECT t.* 
    FROM tours t
    JOIN bookings b ON t.id = b.tour_id
    WHERE b.user_id = :user_id AND t.date < CURRENT_DATE
    ORDER BY t.date DESC
");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$pastTours = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Geçmiş Turlar";
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="profile-page-container">
        <!-- Sidebar kodu buraya -->

        <div class="profile-content">
            <h1><i class="fas fa-history"></i> Geçmiş Turlar</h1>

            <?php if(empty($pastTours)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Henüz katıldığınız bir tur bulunmamaktadır.
                </div>
            <?php else: ?>
                <div class="tour-list">
                    <?php foreach($pastTours as $tour): ?>
                        <div class="tour-card">
                            <!-- Tur bilgilerini göster -->
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>