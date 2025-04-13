<?php
global $conn;
header('Content-Type: application/json');

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS ayarları
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Veritabanı bağlantısı ve auth kontrolü
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

// Gelen veriyi işle
try {
    // FormData ile gelen verileri işle
    $postData = $_POST;
    $files = $_FILES;

    // Gerekli alanları kontrol et
    $requiredFields = ['name', 'short_description', 'description', 'price', 'location', 'type', 'date'];
    foreach ($requiredFields as $field) {
        if (empty($postData[$field])) {
            throw new Exception("$field alanı boş olamaz");
        }
    }

    // Tarih formatını kontrol et
    $date = DateTime::createFromFormat('Y-m-d', $postData['date']);
    if (!$date) {
        throw new Exception("Geçersiz tarih formatı. YYYY-MM-DD olmalıdır.");
    }

    // Resim yükleme işlemi
    $imageName = null;
    if (!empty($files['image']['name'])) {
        $uploadDir = __DIR__ . '/../../assets/images/tours/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = uniqid() . '_' . basename($files['image']['name']);
        $targetPath = $uploadDir . $imageName;

        // Dosya tipi kontrolü
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($files['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Sadece JPEG, PNG veya GIF formatları kabul edilir");
        }

        if (!move_uploaded_file($files['image']['tmp_name'], $targetPath)) {
            throw new Exception("Resim yüklenirken hata oluştu: " . $files['image']['error']);
        }
    }

    // Veritabanına ekle
    $stmt = $conn->prepare("INSERT INTO tours 
        (name, short_description, description, price, location, type, date, image, featured, active, created_at)
        VALUES (:name, :short_desc, :desc, :price, :location, :type, :date, :image, :featured, 1, NOW())");

    $featured = isset($postData['featured']) ? 1 : 0;

    $stmt->execute([
        ':name' => $postData['name'],
        ':short_desc' => $postData['short_description'],
        ':desc' => $postData['description'],
        ':price' => $postData['price'],
        ':location' => $postData['location'],
        ':type' => $postData['type'],
        ':date' => $postData['date'],
        ':image' => $imageName,
        ':featured' => $featured
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Tur başarıyla eklendi',
        'tour_id' => $conn->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'post_data' => $_POST,
        'files_data' => $_FILES,
        'debug_info' => [
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
            'request_method' => $_SERVER['REQUEST_METHOD']
        ]
    ]);
}