<?php
// api/delete_tour.php
global $conn;
require_once __DIR__ . '/../admin/includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM tours WHERE id = ?");
        $stmt->execute([$data['id']]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Tur ID eksik']);
}
?>