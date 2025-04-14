<?php
// includes/db.php

$host = 'localhost';
$dbname = 'vivatour_db';
$user = 'postgres';  // Veya 'alitoksoy' eğer bu kullanıcıyı oluşturduysanız
$password = 'postgresql123';

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    error_log("PostgreSQL bağlantısı başarılı");
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
