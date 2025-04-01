<?php
// admin/manage_tours.php
global $conn;
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$tours = $conn->query("SELECT * FROM tours")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Turları Yönet</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .admin-content {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/admin_header.php'; ?>

<div class="admin-content">
    <h2>Turları Yönet</h2>

    <form id="addTourForm">
        <input type="text" id="tourName" placeholder="Tur Adı" required>
        <input type="number" id="tourPrice" placeholder="Fiyat" required>
        <button type="submit">Tur Ekle</button>
    </form>

    <table id="toursTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tur Adı</th>
            <th>Fiyat</th>
            <th>İşlemler</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tours as $tour): ?>
            <tr>
                <td><?= $tour['id'] ?></td>
                <td><?= htmlspecialchars($tour['name']) ?></td>
                <td><?= $tour['price'] ?> TL</td>
                <td>
                    <button class="edit-btn" data-id="<?= $tour['id'] ?>">Düzenle</button>
                    <button class="delete-btn" data-id="<?= $tour['id'] ?>">Sil</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="/assets/js/ajax.js"></script>
<script>
    document.getElementById('addTourForm').addEventListener('submit', function(e) {
        e.preventDefault();

        fetch('/api/add_tour.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('tourName').value,
                price: document.getElementById('tourPrice').value
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Hata: ' + (data.error || 'Bilinmeyen hata'));
                }
            });
    });
</script>
</body>
</html>