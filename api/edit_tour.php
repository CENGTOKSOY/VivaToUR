<?php
// admin/edit_tour.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_tours.php');
    exit;
}

$tourId = (int)$_GET['id'];

// Tur bilgilerini getir
try {
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$tourId]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tour) {
        die('Tur bulunamadı');
    }
} catch (PDOException $e) {
    die('Veritabanı hatası: ' . $e->getMessage());
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'id' => $tourId,
            'name' => $_POST['name'],
            'short_description' => $_POST['short_description'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'location' => $_POST['location'],
            'type' => $_POST['type'],
            'date' => $_POST['date'],
            'featured' => isset($_POST['featured']),
            'active' => isset($_POST['active'])
        ];

        $response = file_get_contents('../api/update_tour.php', false, stream_context_create([
            'http' => [
                'method' => 'PUT',
                'header' => 'Content-type: application/json',
                'content' => json_encode($data)
            ]
        ]));

        $result = json_decode($response, true);

        if ($result && $result['success']) {
            $successMessage = 'Tur başarıyla güncellendi';
            // Tur bilgilerini yeniden getir
            $stmt->execute([$tourId]);
            $tour = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errorMessage = $result['message'] ?? 'Güncelleme hatası';
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// HTML kısmı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tur Düzenle</title>
    <!-- Stil dosyalarını ekleyin -->
</head>
<body>
<?php include __DIR__ . '/includes/admin_header.php'; ?>

<div class="admin-container">
    <h1>Tur Düzenle: <?= htmlspecialchars($tour['name']) ?></h1>

    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST">
        <!-- Form alanlarını buraya ekleyin -->
        <input type="hidden" name="id" value="<?= $tour['id'] ?>">

        <div class="form-group">
            <label>Tur Adı</label>
            <input type="text" name="name" value="<?= htmlspecialchars($tour['name']) ?>" required>
        </div>

        <!-- Diğer form alanları -->

        <button type="submit" class="btn btn-primary">Güncelle</button>
        <a href="manage_tours.php" class="btn btn-secondary">İptal</a>
    </form>
</div>
</body>
</html>