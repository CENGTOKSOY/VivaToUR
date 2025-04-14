<?php
// includes/db.php

$host = 'localhost';
$port = '5432';
$dbname = 'viva_tour'; // Veritabanı adın
$username = 'postgres'; // Kullanıcı adın
$password = 'postgresql123'; // Şifren

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
