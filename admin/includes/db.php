<?php
// admin/includes/db.php

require_once __DIR__ . '/config.php';

// Hata ayıklama modu
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // PostgreSQL bağlantı DSN (Data Source Name)
    $dsn = "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";user=".DB_USER.";password=".DB_PASS;

    // PDO bağlantısı oluştur
    $conn = new PDO($dsn);

    // PDO ayarları
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Karakter seti ayarı
    $conn->exec("SET NAMES '".DB_CHARSET."'");

    // Test mesajı
    echo "PostgreSQL bağlantısı başarılı!";

} catch (PDOException $e) {
    die("<h2>PostgreSQL Bağlantı Hatası</h2>
        <p><strong>Hata:</strong> " . $e->getMessage() . "</p>
        <p><strong>DSN:</strong> " . $dsn . "</p>
        <p>Lütfen config.php dosyasındaki bilgileri kontrol edin.</p>");
}