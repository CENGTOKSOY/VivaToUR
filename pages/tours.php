<?php include '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>VivaToUR - Turlar</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <header>
        <h1>VivaToUR</h1>
    </header>
    <nav>
        <a href="/pages/index.php">Ana Sayfa</a>
        <a href="/pages/tours.php">Turlar</a>
        <a href="/pages/contact.php">İletişim</a>
    </nav>
    <div class="container">
        <h2>Turlar</h2>
        <div class="tour-list">
            <?php
            $tours = getTours();
            foreach ($tours as $tour) {
                echo "<div class='tour-card'>
                        <h3>{$tour['name']}</h3>
                        <p>{$tour['description']}</p>
                        <p><strong>Fiyat:</strong> {$tour['price']} TL</p>
                      </div>";
            }
            ?>
        </div>
    </div>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/ajax.js"></script>
</body>
</html>