<?php
require 'config.php';

function getTours() {
    global $conn;
    $stmt = $conn->query('SELECT * FROM tours');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addTour($name, $description, $price) {
    global $conn;
    $stmt = $conn->prepare('INSERT INTO tours (name, description, price) VALUES (:name, :description, :price)');
    $stmt->execute(['name' => $name, 'description' => $description, 'price' => $price]);
    return $stmt->rowCount();
}

// Diğer CRUD işlemleri buraya eklenebilir.
?>