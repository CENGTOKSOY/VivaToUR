<?php include '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>VivaToUR - Tur Düzenle</title>
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
        <h2>Tur Düzenle</h2>
        <form id="edit-tour-form">
            <input type="hidden" id="tour-id" name="id">
            <label for="name">Tur Adı:</label>
            <input type="text" id="name" name="name" required>
            <label for="description">Açıklama:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="price">Fiyat:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <button type="submit">Güncelle</button>
        </form>
    </div>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/ajax.js"></script>
</body>
</html>