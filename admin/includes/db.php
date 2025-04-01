<?php
// includes/db.php

$host = 'localhost';
$dbname = 'viva_tour';
$user = 'postgres';
$password = ''; // Şifreniz varsa buraya yazın

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}