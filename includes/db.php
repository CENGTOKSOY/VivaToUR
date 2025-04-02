<?php
// config.php yolunu mutlak yap
$configPath = __DIR__ . '/config.php';
if (!file_exists($configPath)) {
    die("HATA: config.php bulunamadı! Yol: " . $configPath);
}

require_once $configPath;

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}