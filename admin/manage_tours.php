<?php include '../../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>VivaToUR - Turları Yönet</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <header>
        <h1>VivaToUR - Admin Paneli</h1>
    </header>
    <nav>
        <a href="/admin/dashboard.php">Dashboard</a>
        <a href="/admin/manage_tours.php">Turları Yönet</a>
    </nav>
    <div class="container">
        <h2>Turları Yönet</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tur Adı</th>
                    <th>Açıklama</th>
                    <th>Fiyat</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tours = getTours();
                foreach ($tours as $tour) {
                    echo "<tr>
                            <td>{$tour['id']}</td>
                            <td>{$tour['name']}</td>
                            <td>{$tour['description']}</td>
                            <td>{$tour['price']} TL</td>
                            <td>
                                <a href='/pages/edit_tour.php?id={$tour['id']}'>Düzenle</a>
                                <a href='/api/delete_tour.php?id={$tour['id']}'>Sil</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="/assets/js/script.js"></script>
</body>
</html>