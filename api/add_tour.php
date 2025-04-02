<?php
// api/add_tour.php
global $conn;
require_once __DIR__ . '/../admin/includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['name']) && !empty($data['price'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO tours (name, description, price) VALUES (?, '', ?)");
        $stmt->execute([$data['name'], $data['price']]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Eksik bilgi']);
}
?>