<?php
// api/add_tour.php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $conn->prepare("INSERT INTO tours (name, price) VALUES (:name, :price)");
    $stmt->execute([
        'name' => $data['name'],
        'price' => $data['price']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}