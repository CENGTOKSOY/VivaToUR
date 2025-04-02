<?php
// api/update_tour.php
global $conn;
require_once __DIR__ . '/../admin/includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id']) && !empty($data['name']) && !empty($data['price'])) {
    try {
        $stmt = $conn->prepare("UPDATE tours SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['price'], $data['id']]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Eksik bilgi']);
}
?>