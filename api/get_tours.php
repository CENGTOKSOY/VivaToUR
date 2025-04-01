<?php
header('Content-Type: application/json');
include '../includes/db.php';

$tours = getTours();
echo json_encode($tours);
?>