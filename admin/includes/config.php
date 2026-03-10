<?php
// admin/includes/config.php

$host = 'db';
$dbname = 'vivatour_db';
$user = 'postgres';
$password = 'postgresql123';
// Bulut depolama (LocalStack S3) ana URL'miz
define('S3_BASE_URL', 'http://localhost:4566/vivatour-assets/images/');

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>