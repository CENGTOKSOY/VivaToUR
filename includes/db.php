<?php
// includes/db.php

$host = 'db';
$dbname = 'vivatour_db';
$user = 'postgres';
$password = 'postgresql123';

// YENİ VE GÜVENLİ HALİ:
if (!defined('S3_BASE_URL')) {
    define('S3_BASE_URL', 'http://localhost:4566/vivatour-assets/images/');
}
try {
    // Düzeltilmiş bağlantı dizesi
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Schema ayarını ayrı bir sorgu ile yapalım
    $conn->exec("SET search_path TO public");

} catch(PDOException $e) {
    die("<div style='color:red;padding:10px;border:1px solid red;'>Veritabanı bağlantı hatası: " . $e->getMessage() . "</div>");
}