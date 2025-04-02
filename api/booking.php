<?php
global $conn;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['tour_id']) || empty($data['guests'])) {
    echo json_encode(['success' => false, 'error' => 'Eksik bilgi']);
    exit;
}

try {
    $conn->beginTransaction();

    // Rezervasyon oluştur
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_id, guests) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $data['tour_id'], $data['guests']]);

    // Tur bilgilerini getir (fiyat hesaplama için)
    $stmt = $conn->prepare("SELECT price FROM tours WHERE id = ?");
    $stmt->execute([$data['tour_id']]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'booking_id' => $conn->lastInsertId(),
        'total_price' => $tour['price'] * $data['guests']
    ]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Rezervasyon hatası: ' . $e->getMessage()]);
}
?>