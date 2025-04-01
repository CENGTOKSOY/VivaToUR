<?php
header('Content-Type: application/json');
include '../includes/db.php';

$id = $_GET['id'];

if (deleteTour($id)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>