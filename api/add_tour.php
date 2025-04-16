<?php
// api/add_tour.php

header('Content-Type: application/json');

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısı ve auth kontrolü
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// JSON yanıt fonksiyonu
function jsonResponse($success, $message = '', $data = []) {
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

try {
    // Sadece POST isteklerini kabul et
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Sadece POST istekleri kabul edilir');
    }

    // Veritabanı bağlantısını kontrol et
    global $conn;
    if (!$conn) {
        throw new Exception("Veritabanı bağlantısı kurulamadı");
    }

    // Gerekli alanları kontrol et
    $requiredFields = ['name', 'short_description', 'description', 'price', 'location', 'type', 'date'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field alanı boş olamaz");
        }
    }

    // Tarih işleme
    $date = trim($_POST['date']);

    // Tarih formatını kontrol et (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        throw new Exception("Geçersiz tarih formatı. Lütfen YYYY-AA-GG formatında girin.");
    }

    // Tarihin geçerli olup olmadığını kontrol et
    $dateParts = explode('-', $date);
    if (!checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
        throw new Exception("Geçersiz tarih değeri.");
    }

    // Resim yükleme işlemi
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '../assets/images/tours/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Resim klasörü oluşturulamadı");
            }
        }

        // Dosya uzantısını kontrol et
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Sadece JPG, JPEG, PNG ve GIF dosyaları yüklenebilir");
        }

        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            throw new Exception("Resim yüklenirken hata oluştu");
        }
    }

    // Veritabanına ekle
    $stmt = $conn->prepare("INSERT INTO tours 
        (name, short_description, description, price, location, type, date, image, featured, active, created_at)
        VALUES (:name, :short_desc, :desc, :price, :location, :type, :date, :image, :featured, :active, NOW())");

    $result = $stmt->execute([
        ':name' => $_POST['name'],
        ':short_desc' => $_POST['short_description'],
        ':desc' => $_POST['description'],
        ':price' => (float)$_POST['price'],
        ':location' => $_POST['location'],
        ':type' => $_POST['type'],
        ':date' => $date,
        ':image' => $imageName,
        ':featured' => isset($_POST['featured']) ? 1 : 0,
        ':active' => isset($_POST['active']) ? 1 : 0
    ]);

    if (!$result) {
        throw new Exception("Veritabanına ekleme hatası: " . implode(', ', $stmt->errorInfo()));
    }

    echo jsonResponse(true, 'Tur başarıyla eklendi', [
        'tour_id' => $conn->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo jsonResponse(false, $e->getMessage());
}