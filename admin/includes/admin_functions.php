<?php
// admin/includes/admin_functions.php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Tüm turları getirir
 */
function getTours() {
    global $conn;
    try {
        $sql = "SELECT id, name, description, price, image, location, 
                       to_char(date, 'DD.MM.YYYY') as formatted_date,
                       to_char(created_at, 'DD.MM.YYYY HH24:MI') as created_at,
                       to_char(updated_at, 'DD.MM.YYYY HH24:MI') as updated_at
                FROM tours 
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] getTours Hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * ID ile tur getirir
 */
function getTourById($id) {
    global $conn;
    try {
        $sql = "SELECT id, name, description, price, image, location, date,
                       to_char(created_at, 'YYYY-MM-DD') as created_at,
                       to_char(updated_at, 'YYYY-MM-DD') as updated_at
                FROM tours 
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] getTourById Hatası (ID:$id): " . $e->getMessage());
        return null;
    }
}

/**
 * Yeni tur ekler
 */
function addTour($data) {
    global $conn;
    try {
        $sql = "INSERT INTO tours 
                (name, description, price, image, location, date)
                VALUES (:name, :description, :price, :image, :location, :date)
                RETURNING id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':image' => $data['image'] ?? null,
            ':location' => $data['location'] ?? null,
            ':date' => $data['date'] ?? null
        ]);

        return $stmt->fetch()['id'];

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] addTour Hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Tur günceller
 */
function updateTour($id, $data) {
    global $conn;
    try {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE tours SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $conn->prepare($sql);

        return $stmt->execute($params);

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] updateTour Hatası (ID:$id): " . $e->getMessage());
        return false;
    }
}

/**
 * Tur siler
 */
function deleteTour($id) {
    global $conn;
    try {
        $sql = "DELETE FROM tours WHERE id = :id";
        $stmt = $conn->prepare($sql);

        return $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] deleteTour Hatası (ID:$id): " . $e->getMessage());
        return false;
    }
}

/**
 * Resim yükler
 */
function uploadTourImage($file) {
    require_once __DIR__ . '/config.php';

    // Hata kontrolü
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Dosya yükleme hatası: " . $file['error']);
    }

    // Dosya tipi kontrolü
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
        throw new Exception("Sadece JPG, PNG veya WebP formatları kabul edilir");
    }

    // Dosya boyutu kontrolü
    if ($file['size'] > MAX_IMAGE_SIZE) {
        throw new Exception("Maksimum dosya boyutu 2MB olabilir");
    }

    // Hedef dizin kontrolü
    if (!file_exists(TOURS_IMAGE_DIR)) {
        mkdir(TOURS_IMAGE_DIR, 0755, true);
    }

    // Yeni dosya adı
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('tour_') . '.' . $extension;
    $targetPath = TOURS_IMAGE_DIR . $filename;

    // Dosyayı taşı
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // macOS izin ayarları
        chmod($targetPath, 0644);
        return $filename;
    }

    throw new Exception("Dosya taşınamadı");
}

/**
 * Admin giriş kontrolü
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Admin yetki kontrolü
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>