<?php
header('Content-Type: application/json');
include '../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];
$description = $data['description'];
$price = $data['price'];

if (addTour($name, $description, $price)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>