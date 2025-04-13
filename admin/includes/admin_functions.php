<?php
// admin/includes/admin_functions.php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Tüm turları getirir (filtreleme desteği ile)
 */
function getTours($filters = []) {
    global $conn;
    try {
        $where = [];
        $params = [];

        // Filtreleme parametreleri
        if (!empty($filters['type'])) {
            $where[] = "type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['location'])) {
            $where[] = "location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['price_range'])) {
            if ($filters['price_range'] === '0-1000') {
                $where[] = "price BETWEEN 0 AND 1000";
            } elseif ($filters['price_range'] === '1000-2000') {
                $where[] = "price BETWEEN 1000 AND 2000";
            } elseif ($filters['price_range'] === '2000+') {
                $where[] = "price > 2000";
            }
        }

        if (!empty($filters['date'])) {
            $where[] = "DATE(date) = ?";
            $params[] = date('Y-m-d', strtotime($filters['date']));
        }

        if (!empty($filters['featured'])) {
            $where[] = "featured = ?";
            $params[] = $filters['featured'];
        }

        $sql = "SELECT id, name, description, short_description, price, image, location, type, date, featured,
                       to_char(date, 'DD.MM.YYYY') as formatted_date,
                       to_char(created_at, 'DD.MM.YYYY HH24:MI') as created_at,
                       to_char(updated_at, 'DD.MM.YYYY HH24:MI') as updated_at
                FROM tours";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] getTours Hatası: " . $e->getMessage());
        return [];
    }
}

function checkAdminAccess() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: manage_tours.php');
        exit;
    }
}
/**
 * ID ile detaylı tur getirir
 */
function getTourById($id) {
    global $conn;
    try {
        $sql = "SELECT id, name, description, short_description, price, image, location, type, date, featured,
                       to_char(date, 'DD.MM.YYYY') as formatted_date,
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
 * Yeni tur ekler (resim yükleme desteği ile)
 */
function addTour($data, $file = null) {
    global $conn;
    try {
        // Resim yükleme
        $imageName = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imageName = uploadTourImage($file);
        }

        $sql = "INSERT INTO tours 
                (name, description, short_description, price, image, location, type, date, featured)
                VALUES (:name, :description, :short_description, :price, :image, :location, :type, :date, :featured)
                RETURNING id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':short_description' => $data['short_description'] ?? null,
            ':price' => $data['price'],
            ':image' => $imageName,
            ':location' => $data['location'] ?? null,
            ':type' => $data['type'] ?? 'cultural',
            ':date' => $data['date'] ?? null,
            ':featured' => isset($data['featured']) ? 1 : 0
        ]);

        return $stmt->fetch()['id'];

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] addTour Hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Tur günceller (resim yükleme desteği ile)
 */
function updateTour($id, $data, $file = null) {
    global $conn;
    try {
        // Mevcut tur bilgilerini al
        $currentTour = getTourById($id);
        if (!$currentTour) {
            throw new Exception("Tur bulunamadı");
        }

        // Resim yükleme
        $imageName = $currentTour['image'];
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            // Eski resmi sil
            if ($imageName) {
                $imagePath = TOURS_IMAGE_DIR . $imageName;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $imageName = uploadTourImage($file);
        }

        $fields = [];
        $params = [':id' => $id];

        // Güncellenecek alanlar
        $updateFields = [
            'name' => $data['name'] ?? $currentTour['name'],
            'description' => $data['description'] ?? $currentTour['description'],
            'short_description' => $data['short_description'] ?? $currentTour['short_description'],
            'price' => $data['price'] ?? $currentTour['price'],
            'image' => $imageName,
            'location' => $data['location'] ?? $currentTour['location'],
            'type' => $data['type'] ?? $currentTour['type'],
            'date' => $data['date'] ?? $currentTour['date'],
            'featured' => isset($data['featured']) ? 1 : 0
        ];

        foreach ($updateFields as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE tours SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $conn->prepare($sql);

        return $stmt->execute($params);

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] updateTour Hatası (ID:$id): " . $e->getMessage());
        return false;
    }
}

/**
 * Tur siler (ilişkili resmi de siler)
 */
function deleteTour($id) {
    global $conn;
    try {
        // Tur bilgilerini al (resim silmek için)
        $tour = getTourById($id);
        if (!$tour) {
            throw new Exception("Tur bulunamadı");
        }

        // Resmi sil
        if (!empty($tour['image'])) {
            $imagePath = TOURS_IMAGE_DIR . $tour['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $sql = "DELETE FROM tours WHERE id = :id";
        $stmt = $conn->prepare($sql);

        return $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] deleteTour Hatası (ID:$id): " . $e->getMessage());
        return false;
    }
}

/**
 * Resim yükler (güvenlik kontrolleri ile)
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

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mime, $allowedTypes)) {
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
    $extension = $allowedTypes[$mime];
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

/**
 * Tur türlerini getirir
 */
function getTourTypes() {
    return [
        'cultural' => 'Kültürel Tur',
        'festival' => 'Festival Turu',
        'adaptation' => 'Adaptasyon Turu',
        'historical' => 'Tarihi Tur',
        'nature' => 'Doğa Turu',
        'gastronomy' => 'Gastronomi Turu'
    ];
}

/**
 * Öne çıkan turları getirir
 */
function getFeaturedTours($limit = 3) {
    global $conn;
    try {
        $sql = "SELECT id, name, short_description, price, image, location, 
                       to_char(date, 'DD.MM.YYYY') as formatted_date
                FROM tours 
                WHERE featured = true
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$limit]);

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] getFeaturedTours Hatası: " . $e->getMessage());
        return [];
    }
}
?>