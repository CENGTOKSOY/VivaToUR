<?php
global $conn;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['tour_id'])) {
    echo json_encode(['success' => false, 'error' => 'Tur ID eksik']);
    exit;
}

try {
    // Favori var mı kontrol etme
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND tour_id = ?");
    $stmt->execute([$userId, $data['tour_id']]);

    if ($stmt->rowCount() > 0) {
        // Favoriyi kaldır
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND tour_id = ?");
        $stmt->execute([$userId, $data['tour_id']]);
        echo json_encode(['success' => true, 'is_favorite' => false]);
    } else {
        // Favoriye ekle
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, tour_id) VALUES (?, ?)");
        $stmt->execute([$userId, $data['tour_id']]);
        echo json_encode(['success' => true, 'is_favorite' => true]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'İşlem hatası: ' . $e->getMessage()]);
}
?>