<?php
global $conn;
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Gerekli alanlar
    $required = ['name', 'email', 'password', 'phone'];
    $missing = array_diff($required, array_keys($input));

    if (!empty($missing)) {
        $response['message'] = 'Missing required fields: ' . implode(', ', $missing);
        echo json_encode($response);
        exit;
    }

    try {
        // Email kontrolü
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);

        if ($stmt->rowCount() > 0) {
            $response['message'] = 'Email already exists';
            echo json_encode($response);
            exit;
        }

        // Kullanıcıyı kaydet
        $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $input['name'],
            $input['email'],
            $hashedPassword,
            $input['phone']
        ]);

        $response['success'] = true;
        $response['message'] = 'Registration successful';
        $response['userId'] = $conn->lastInsertId();

    } catch (PDOException $e) {
        $response['message'] = 'Registration failed: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);