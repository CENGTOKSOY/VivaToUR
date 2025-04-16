<?php
// includes/db.php

$host = 'localhost';
$dbname = 'vivatour_db';
$user = 'postgres';
$password = 'postgresql123';

try {
    // Düzeltilmiş bağlantı dizesi
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Schema ayarını ayrı bir sorgu ile yapalım
    $conn->exec("SET search_path TO public");

} catch(PDOException $e) {
    die("<div style='color:red;padding:10px;border:1px solid red;'>Veritabanı bağlantı hatası: " . $e->getMessage() . "</div>");
}