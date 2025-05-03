<?php
global $conn;
require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../includes/db.php';
session_start();

// Kullanıcı giriş durumunu kontrol etme
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// API isteği kontrolü
if (isset($_GET['api']) && $_GET['api'] == '1') {
    header('Content-Type: application/json');

    try {
        $query = "SELECT * FROM tours WHERE active = true";
        $params = [];

        // Filtreleme parametreleri
        $typeFilter = $_GET['type'] ?? '';
        $locationFilter = $_GET['location'] ?? '';
        $dateFilter = $_GET['date'] ?? '';

        if (!empty($typeFilter)) {
            $query .= " AND type = :type";
            $params[':type'] = $typeFilter;
        }

        if (!empty($locationFilter)) {
            $query .= " AND location LIKE :location";
            $params[':location'] = '%' . $locationFilter . '%';
        }

        if (!empty($dateFilter)) {
            $query .= " AND DATE(date) = :date";
            $params[':date'] = $dateFilter;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tur verilerini formatla
        $formattedTours = [];
        foreach ($tours as $tour) {
            $imageParts = explode('_', $tour['image']);
            $imageFile = end($imageParts);
            $imagePath = !empty($tour['image']) ? '../assets/images/tours/' . $imageFile : '../assets/images/tour-default.jpg';

            $typeLabels = [
                'cultural' => 'Kültürel',
                'festival' => 'Festival',
                'adaptation' => 'Adaptasyon',
                'historical' => 'Tarihi'
            ];

            $formattedTours[] = [
                'id' => $tour['id'],
                'name' => htmlspecialchars($tour['name']),
                'location' => htmlspecialchars($tour['location']),
                'date' => date('d.m.Y', strtotime($tour['date'])),
                'type' => $typeLabels[$tour['type']] ?? $tour['type'],
                'short_description' => htmlspecialchars($tour['short_description']),
                'price' => number_format($tour['price'], 2),
                'featured' => $tour['featured'],
                'image' => $imagePath,
                'is_logged_in' => $isLoggedIn
            ];
        }

        echo json_encode(['success' => true, 'tours' => $formattedTours]);
    } catch (PDOException $e) {
        error_log("Turlar çekilirken hata: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Turlar çekilirken hata oluştu']);
    }
    exit;
}

// Kullanıcı rezervasyon bilgileri
if ($isLoggedIn) {
    try {
        // Aktif rezervasyonlar
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND booking_date > NOW()");
        $stmt->execute([$userId]);
        $activeBookings = $stmt->fetchColumn();

        // Geçmiş turlar
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND booking_date <= NOW()");
        $stmt->execute([$userId]);
        $pastTours = $stmt->fetchColumn();

        // Favori turlar
        $stmt = $conn->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        $favorites = $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Kullanıcı verileri çekilirken hata: " . $e->getMessage());
        $activeBookings = $pastTours = $favorites = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tüm Turlar | VivaToUR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --viva-orange: #FF7A00;
            --viva-orange-light: #FFE8D5;
            --viva-dark: #333333;
            --viva-gray: #F5F5F5;
            --viva-white: #FFFFFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--viva-gray);
            color: var(--viva-dark);
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--viva-orange), #FF9A3E);
            color: white;
            padding: 1rem 0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--viva-orange);
            font-weight: bold;
        }

        nav {
            background-color: var(--viva-white);
            padding: 1rem;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav a {
            color: var(--viva-dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav a:hover, nav a.active {
            background-color: var(--viva-orange-light);
            color: var(--viva-orange);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .hero {
            background: url('../assets/images/tour-default.jpg') no-repeat center center/cover;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 122, 0, 0.6);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 2rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        /* Yeni Filtreleme Stilleri */
        .filters-container {
            background: var(--viva-white);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .filters-header {
            background: var(--viva-orange);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .filters-header h3 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filters-content {
            padding: 1.5rem;
            border-top: 1px solid var(--viva-orange-light);
            transition: max-height 0.3s ease;
            overflow: hidden;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .filter-group {
            margin-bottom: 0;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: var(--viva-dark);
            font-size: 0.95rem;
        }

        .filter-select, .filter-input {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid var(--viva-orange-light);
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            background: white;
            transition: all 0.3s;
        }

        .filter-select:focus, .filter-input:focus {
            border-color: var(--viva-orange);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.2);
        }

        .filter-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            grid-column: 1 / -1;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }

        .filter-button {
            background: var(--viva-orange);
            color: white;
            border: none;
            padding: 0.85rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            justify-content: center;
        }

        .filter-button:hover {
            background: #e66d00;
            transform: translateY(-2px);
        }

        .reset-button {
            background: white;
            color: var(--viva-orange);
            border: 2px solid var(--viva-orange);
            padding: 0.85rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            justify-content: center;
        }

        .reset-button:hover {
            background: var(--viva-orange-light);
        }

        /* Select wrapper for custom arrow */
        .select-wrapper {
            position: relative;
        }

        .select-wrapper::after {
            content: "▾";
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            color: var(--viva-orange);
            pointer-events: none;
        }

        /* Date picker custom style */
        input[type="date"] {
            position: relative;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            right: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .date-input-wrapper {
            position: relative;
        }

        .date-input-wrapper::after {
            content: "📅";
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        /* Tur Grid Stilleri */
        .section-title {
            color: var(--viva-orange);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .tour-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .tour-card {
            background-color: var(--viva-white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .tour-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .tour-price {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: var(--viva-orange);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .tour-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: var(--viva-orange);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.75rem;
        }

        .tour-content {
            padding: 1.5rem;
        }

        .tour-title {
            margin: 0 0 0.5rem;
            color: var(--viva-dark);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .tour-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            color: #666;
        }

        .tour-meta i {
            color: var(--viva-orange);
            margin-right: 0.25rem;
        }

        .tour-description {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .tour-actions {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: white;
            color: var(--viva-orange);
        }

        .btn-primary:hover {
            background: var(--viva-orange-light);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .favorite-btn {
            background: transparent;
            color: var(--viva-orange);
            border: 1px solid var(--viva-orange);
        }

        .favorite-btn:hover {
            background: var(--viva-orange-light);
        }

        .favorite-btn.active {
            background: var(--viva-orange);
            color: white;
        }

        .empty-message {
            text-align: center;
            grid-column: 1 / -1;
            padding: 2rem;
            color: #666;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }

        .page-link {
            padding: 0.5rem 1rem;
            border: 1px solid var(--viva-orange-light);
            border-radius: 4px;
            color: var(--viva-orange);
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-link:hover, .page-link.active {
            background: var(--viva-orange);
            color: white;
            border-color: var(--viva-orange);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 4px solid rgba(255, 122, 0, 0.3);
            border-radius: 50%;
            border-top: 4px solid var(--viva-orange);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        footer {
            background: var(--viva-dark);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .social-links {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            transition: transform 0.3s;
        }

        .social-links a:hover {
            transform: translateY(-3px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero {
                height: 300px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .header-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .user-menu {
                justify-content: center;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <a href="index.php" class="logo">VivaToUR</a>
        <div class="user-menu">
            <?php if($isLoggedIn): ?>
                <span>Hoş geldiniz, <?= htmlspecialchars($userName) ?></span>
                <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <a href="user/profile.php" style="color: white;"><i class="fas fa-user"></i></a>
                <a href="auth/logout.php" style="color: white;"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Giriş Yap</a>
                <a href="auth/register.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="tours.php" class="active"><i class="fas fa-umbrella-beach"></i> Turlar</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> İletişim</a>
    <a href="about.php"><i class="fas fa-info-circle"></i> Hakkımızda</a>
    <?php if($isLoggedIn): ?>
        <a href="user/bookings.php"><i class="fas fa-suitcase"></i> Rezervasyonlarım</a>
    <?php endif; ?>
</nav>

<div class="container">
    <section class="hero">
        <div class="hero-content">
            <h1>Tüm Kültür Turları</h1>
            <p>Türkiye'nin dört bir yanındaki eşsiz kültür deneyimlerini keşfedin</p>
        </div>
    </section>

    <section class="filters-container">
        <div class="filters-header">
            <h3><i class="fas fa-sliders-h"></i> Filtreleme Seçenekleri</h3>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="filters-content">
            <form id="filter-form">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="type" class="filter-label">Tur Türü</label>
                        <div class="select-wrapper">
                            <select id="type" name="type" class="filter-select">
                                <option value="">Tüm Tur Türleri</option>
                                <option value="cultural">Kültürel Tur</option>
                                <option value="festival">Festival Turu</option>
                                <option value="adaptation">Adaptasyon Turu</option>
                                <option value="historical">Tarihi Tur</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="location" class="filter-label">Lokasyon</label>
                        <input type="text" id="location" name="location" class="filter-input"
                               placeholder="İstanbul, Ankara, İzmir...">
                    </div>

                    <div class="filter-group">
                        <label for="date" class="filter-label">Tarih</label>
                        <div class="date-input-wrapper">
                            <input type="date" id="date" name="date" class="filter-input">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="filter-button">
                            <i class="fas fa-search"></i> Filtrele
                        </button>
                        <button type="reset" id="reset-filters" class="reset-button">
                            <i class="fas fa-undo"></i> Sıfırla
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="all-tours">
        <h2 class="section-title"><i class="fas fa-umbrella-beach"></i> Tüm Turlar</h2>

        <div id="loading-spinner" class="loading-spinner">
            <div class="spinner"></div>
            <p>Turlar yükleniyor...</p>
        </div>

        <div id="empty-message" class="empty-message" style="display: none;">
            <p>Seçtiğiniz filtrelerle eşleşen tur bulunamadı.</p>
            <button id="show-all-tours" class="btn btn-primary">Tüm Turları Göster</button>
        </div>

        <div class="tour-grid" id="tour-grid">
            <!-- Turlar buraya AJAX ile yüklenecek -->
        </div>
    </section>
</div>

<footer>
    <p>&copy; 2025 VivaToUR - Tüm Hakları Saklıdır</p>
    <div class="social-links">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-whatsapp"></i></a>
    </div>
</footer>

<script src="../assets/js/script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtreleme paneli aç/kapa
        const filtersHeader = document.querySelector('.filters-header');
        if (filtersHeader) {
            filtersHeader.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const icon = this.querySelector('.fa-chevron-down, .fa-chevron-up');

                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                } else {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                }
            });

            // Başlangıçta açık olsun
            const content = document.querySelector('.filters-content');
            const icon = document.querySelector('.filters-header .fa-chevron-down');
            if (content && icon) {
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            }
        }

        // URL'den filtre parametrelerini al
        function getFilterParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                type: params.get('type') || '',
                location: params.get('location') || '',
                date: params.get('date') || ''
            };
        }

        // Filtre formunu URL'den gelen parametrelerle doldur
        function populateFormFromUrl() {
            const filters = getFilterParams();
            document.getElementById('type').value = filters.type;
            document.getElementById('location').value = filters.location;
            document.getElementById('date').value = filters.date;
        }

        // Sayfa yüklendiğinde formu doldur ve turları getir
        populateFormFromUrl();
        fetchTours();

        // Filtreleme formu gönderimi
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const type = document.getElementById('type').value;
            const location = document.getElementById('location').value;
            const date = document.getElementById('date').value;

            // URL'yi güncelle (sayfa yenilenmeden)
            const params = new URLSearchParams();
            if (type) params.set('type', type);
            if (location) params.set('location', location);
            if (date) params.set('date', date);

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);

            // Turları getir
            fetchTours();
        });

        // Filtreleri sıfırla
        document.getElementById('reset-filters').addEventListener('click', function() {
            window.history.pushState({}, '', window.location.pathname);
            populateFormFromUrl();
            fetchTours();
        });

        // Tüm turları göster butonu
        document.getElementById('show-all-tours').addEventListener('click', function() {
            window.history.pushState({}, '', window.location.pathname);
            populateFormFromUrl();
            fetchTours();
        });

        // Turları getir fonksiyonu
        function fetchTours() {
            const loadingSpinner = document.getElementById('loading-spinner');
            const emptyMessage = document.getElementById('empty-message');
            const tourGrid = document.getElementById('tour-grid');

            loadingSpinner.style.display = 'block';
            emptyMessage.style.display = 'none';
            tourGrid.innerHTML = '';

            const filters = getFilterParams();
            const params = new URLSearchParams();
            params.set('api', '1');
            if (filters.type) params.set('type', filters.type);
            if (filters.location) params.set('location', filters.location);
            if (filters.date) params.set('date', filters.date);

            fetch(`tours.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.style.display = 'none';

                    if (data.success && data.tours.length > 0) {
                        renderTours(data.tours);
                    } else {
                        emptyMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    loadingSpinner.style.display = 'none';
                    emptyMessage.style.display = 'block';
                });
        }

        // Turları render et
        function renderTours(tours) {
            const tourGrid = document.getElementById('tour-grid');
            tourGrid.innerHTML = '';

            tours.forEach(tour => {
                const tourCard = document.createElement('div');
                tourCard.className = 'tour-card';
                tourCard.innerHTML = `
                    <div class="tour-image" style="background-image: url('${tour.image}');">
                        ${tour.featured ? '<span class="tour-badge">Öne Çıkan</span>' : ''}
                        <span class="tour-price">${tour.price} TL</span>
                    </div>
                    <div class="tour-content">
                        <h3 class="tour-title">${tour.name}</h3>
                        <div class="tour-meta">
                            <span><i class="fas fa-map-marker-alt"></i> ${tour.location}</span>
                            <span><i class="fas fa-calendar-alt"></i> ${tour.date}</span>
                        </div>
                        <div class="tour-meta">
                            <span><i class="fas fa-tag"></i> ${tour.type}</span>
                        </div>
                        <p class="tour-description">${tour.short_description}</p>
                        <div class="tour-actions">
                            <a href="detail.php?id=${tour.id}" class="btn btn-primary btn-sm">Detaylar</a>
                            ${tour.is_logged_in ? `
                                <button class="btn btn-secondary btn-sm favorite-btn" data-tour-id="${tour.id}">
                                    <i class="far fa-heart"></i> Favori
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                tourGrid.appendChild(tourCard);
            });

            // Favori butonlarına event listener ekle
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tourId = this.dataset.tourId;
                    const icon = this.querySelector('i');

                    fetch('../api/toggle_favorite.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ tour_id: tourId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                if(data.is_favorite) {
                                    icon.classList.replace('far', 'fas');
                                    this.classList.add('active');
                                } else {
                                    icon.classList.replace('fas', 'far');
                                    this.classList.remove('active');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Hata:', error);
                        });
                });
            });
        }

        // Popstate event (geri/ileri butonları için)
        window.addEventListener('popstate', function() {
            populateFormFromUrl();
            fetchTours();
        });
    });
</script>
</body>
</html>