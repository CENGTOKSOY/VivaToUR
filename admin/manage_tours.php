<?php
// admin/manage_tours.php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

global $conn;
$tours = $conn->query("SELECT * FROM tours")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Turları Yönet</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
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
            text-align: left;
        }
        input[type="text"], input[type="number"] {
            padding: 8px;
            margin-right: 10px;
            width: 200px;
        }
        button {
            padding: 8px 12px;
            cursor: pointer;
        }
        .edit-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/admin_header.php'; ?>

<div class="admin-content">
    <h2>Turları Yönet</h2>

    <!-- Tur Ekleme Formu -->
    <form id="addTourForm">
        <input type="text" id="tourName" placeholder="Tur Adı" required>
        <input type="number" id="tourPrice" placeholder="Fiyat" required>
        <button type="submit">Tur Ekle</button>
    </form>

    <!-- Tur Listesi Tablosu -->
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. TUR EKLEME İŞLEMİ
        document.getElementById('addTourForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('tourName').value;
            const price = document.getElementById('tourPrice').value;

            fetch('../../api/add_tour.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, price })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tur başarıyla eklendi!');
                        location.reload(); // Sayfayı yenile
                    } else {
                        alert('Hata: ' + (data.error || 'Tur eklenemedi!'));
                    }
                })
                .catch(error => {
                    alert('İstek hatası: ' + error);
                });
        });

        // 2. TUR SİLME İŞLEMİ
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Bu turu silmek istediğinize emin misiniz?')) {
                    const id = this.dataset.id;

                    fetch('../../api/delete_tour.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Silme hatası: ' + (data.error || 'Bilinmeyen hata'));
                            }
                        });
                }
            });
        });

        // 3. TUR DÜZENLEME İŞLEMİ
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = this.dataset.id;
                const name = row.querySelector('td:nth-child(2)').textContent;
                const price = row.querySelector('td:nth-child(3)').textContent.replace(' TL', '');

                // Düzenleme formunu oluştur
                const form = document.createElement('form');
                form.className = 'edit-form';
                form.innerHTML = `
                    <input type="text" value="${name}" class="edit-name" required>
                    <input type="number" value="${price}" class="edit-price" required>
                    <button type="submit">Kaydet</button>
                    <button type="button" class="cancel-edit">İptal</button>
                `;

                // İşlemler hücresini form ile değiştir
                const actionsCell = row.querySelector('td:last-child');
                actionsCell.innerHTML = '';
                actionsCell.appendChild(form);

                // Kaydet butonu işlevi
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    fetch('../../api/update_tour.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id: id,
                            name: form.querySelector('.edit-name').value,
                            price: form.querySelector('.edit-price').value
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Güncelleme hatası: ' + (data.error || 'Bilinmeyen hata'));
                            }
                        });
                });

                // İptal butonu işlevi
                form.querySelector('.cancel-edit').addEventListener('click', function() {
                    location.reload();
                });
            });
        });
    });
</script>
</body>
</html>