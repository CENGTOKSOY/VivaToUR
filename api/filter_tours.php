<?php
global $conn;
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    // Filtre parametrelerini al
    $destination = $_GET['destination'] ?? null;
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;
    $priceMin = $_GET['price_min'] ?? null;
    $priceMax = $_GET['price_max'] ?? null;

    // SQL sorgusunu oluştur
    $sql = "SELECT * FROM tours WHERE 1=1";
    $params = [];

    if ($destination) {
        $sql .= " AND destination LIKE :destination";
        $params[':destination'] = "%$destination%";
    }

    if ($startDate && $endDate) {
        $sql .= " AND start_date BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $startDate;
        $params[':end_date'] = $endDate;
    }

    if ($priceMin) {
        $sql .= " AND price >= :price_min";
        $params[':price_min'] = $priceMin;
    }

    if ($priceMax) {
        $sql .= " AND price <= :price_max";
        $params[':price_max'] = $priceMax;
    }

    // Sorguyu çalıştır
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $tours,
        'count' => count($tours)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}