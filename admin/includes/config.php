<?php
// admin/includes/config.php

$host = 'localhost';
$dbname = 'vivatour_db';
$user = 'postgres';
$password = 'postgresql123';

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>