<?php
// api/update_tour.php

global $conn;
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function jsonResponse($success, $message = '', $data = []) {
    http_response_code($success ? 200 : 400);
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        throw new Exception('Sadece PUT istekleri kabul edilir', 405);
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Geçersiz JSON verisi');
    }

    $requiredFields = ['id', 'name', 'price', 'short_description', 'location', 'type', 'date'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("$field alanı boş olamaz");
        }
    }

    // Veritabanında tur var mı kontrol et
    $checkStmt = $conn->prepare("SELECT id FROM tours WHERE id = ?");
    $checkStmt->execute([$input['id']]);
    if ($checkStmt->rowCount() === 0) {
        throw new Exception("Tur bulunamadı", 404);
    }

    // Güncelleme işlemi
    $stmt = $conn->prepare("UPDATE tours SET 
        name = ?, 
        short_description = ?, 
        description = ?, 
        price = ?, 
        location = ?, 
        type = ?, 
        date = ?, 
        featured = ?, 
        active = ?,
        updated_at = NOW()
        WHERE id = ?");

    $result = $stmt->execute([
        $input['name'],
        $input['short_description'],
        $input['description'] ?? '',
        (float)$input['price'],
        $input['location'],
        $input['type'],
        $input['date'],
        isset($input['featured']) ? 1 : 0,
        isset($input['active']) ? 1 : 0,
        $input['id']
    ]);

    if (!$result) {
        throw new Exception("Güncelleme hatası: " . implode(', ', $stmt->errorInfo()));
    }

    echo jsonResponse(true, 'Tur başarıyla güncellendi');

} catch (Exception $e) {
    echo jsonResponse(false, $e->getMessage());
}