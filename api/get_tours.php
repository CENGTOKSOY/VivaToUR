<?php
// api/get_tours.php

header('Content-Type: application/json');
require_once __DIR__ . '/../admin/includes/db.php';

function getTours() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT * FROM tours ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

$tours = getTours();
echo json_encode($tours);