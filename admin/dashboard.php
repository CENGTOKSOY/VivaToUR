<?php
// admin/dashboard.php

// 1. Oturum ve güvenlik kontrolleri
global $conn;
session_start();
ob_start();

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/admin_functions.php';

// Giriş kontrolü
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// 2. Çıkış işlemi
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    ob_end_clean();
    header("Location: login.php");
    exit;
}

// 3. Veritabanı sorguları (SADECE users ve tours tablolarını kullanarak)
try {
    // Veritabanı bağlantısı kontrolü
    if (!$conn) {
        throw new PDOException("Veritabanı bağlantısı kurulamadı");
    }

    // AKTİF TURLAR (aktif olan tüm turlar)
    $activeTours = $conn->query("
        SELECT COUNT(*) 
        FROM public.tours 
        WHERE active = true
    ")->fetchColumn();

    // TOPLAM MÜŞTERİ (users tablosundaki tüm kayıtlar)
    $totalCustomers = $conn->query("
        SELECT COUNT(*) 
        FROM public.users
    ")->fetchColumn();

    // AKTİF MÜŞTERİLER (son 1 yılda giriş yapanlar)
    $activeCustomers = $conn->query("
        SELECT COUNT(*) 
        FROM public.users 
        WHERE last_login >= NOW() - INTERVAL '1 year'
    ")->fetchColumn();

    // YENİ KAYITLI MÜŞTERİLER (son 7 gün)
    $newCustomers = $conn->query("
        SELECT COUNT(*) 
        FROM public.users 
        WHERE created_at >= NOW() - INTERVAL '7 days'
    ")->fetchColumn();

    // YAKLAŞAN TURLAR (önümüzdeki 5 tur)
    $upcomingTours = $conn->query("
        SELECT 
            id, 
            name, 
            date, 
            price,
            featured,
            image
        FROM public.tours
        WHERE active = true 
        AND date >= CURRENT_DATE
        ORDER BY date ASC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // SON KAYITLI MÜŞTERİLER (son 5 kayıt)
    $recentCustomers = $conn->query("
        SELECT 
            id, 
            name, 
            email, 
            to_char(created_at, 'DD.MM.YYYY') as created_at
        FROM public.users
        ORDER BY created_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Dashboard Hatası: " . $e->getMessage());
    $activeTours = $totalCustomers = $activeCustomers = $newCustomers = 0;
    $upcomingTours = $recentCustomers = [];
    $error_message = "Sistem hatası: Lütfen daha sonra tekrar deneyin.";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VivaToUR - Admin Paneli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF7A00;
            --primary-hover: #E56D00;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --border-color: #e0e0e0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: var(--light-bg);
            color: var(--dark-text);
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            padding-top: 20px;
            z-index: 1000;
        }

        .logo {
            text-align: center;
            padding: 20px 0 40px;
        }

        .logo img {
            width: 150px;
            transition: transform 0.3s;
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-menu li a {
            display: block;
            padding: 15px 30px;
            color: var(--dark-text);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-menu li a:hover, .nav-menu li a.active {
            background: #FFE8D5;
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
        }

        .nav-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: margin 0.3s;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .header h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 28px;
        }

        .user-info {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 5px;
            z-index: 1;
        }

        .user-dropdown a {
            padding: 12px 16px;
            display: block;
            color: var(--dark-text);
            text-decoration: none;
            transition: background 0.3s;
        }

        .user-dropdown a:hover {
            background: #FFE8D5;
            color: var(--primary-color);
        }

        .user-info:hover .user-dropdown {
            display: block;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            border-top: 4px solid var(--primary-color);
        }

        .stat-card.green {
            border-top-color: var(--success-color);
        }

        .stat-card.blue {
            border-top-color: var(--secondary-color);
        }

        .stat-card.purple {
            border-top-color: #6f42c1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin-top: 0;
            color: var(--dark-text);
            font-size: 14px;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
            color: var(--dark-text);
        }

        .stat-card .change {
            font-size: 14px;
            display: flex;
            align-items: center;
            color: var(--dark-text);
            opacity: 0.8;
        }

        .stat-card .change i {
            margin-right: 5px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            margin: 0;
            color: var(--primary-color);
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            text-align: left;
            padding: 12px 15px;
            background: #FFE8D5;
            color: var(--primary-color);
            font-weight: 600;
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover {
            background-color: rgba(255, 122, 0, 0.05);
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 5px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 122, 0, 0.2);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .customer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .tour-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            background: var(--primary-color);
            color: white;
        }

        .error-message {
            background: #ffebee;
            color: #d32f2f;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #f44336;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @media (max-width: 992px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-info {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="../assets/images/logo.png" alt="VivaToUR Logo">
    </div>

    <ul class="nav-menu">
        <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="manage_tours.php"><i class="fas fa-umbrella-beach"></i> Turları Yönet</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Müşteriler</a></li>
        <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>Dashboard</h1>
        <div class="user-info">
            <img src="../assets/images/admin-avatar.png" alt="Admin">
            <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
            <div class="user-dropdown">
                <a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
            </div>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="error-message fade-in">
            <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card fade-in">
            <h3>Aktif Turlar</h3>
            <div class="value"><?= $activeTours ?></div>
            <div class="change">
                <i class="fas fa-calendar-alt"></i> Şu an aktif olan turlar
            </div>
        </div>

        <div class="stat-card green fade-in">
            <h3>Toplam Müşteri</h3>
            <div class="value"><?= $totalCustomers ?></div>
            <div class="change">
                <i class="fas fa-users"></i> Sistemde kayıtlı müşteriler
            </div>
        </div>

        <div class="stat-card blue fade-in">
            <h3>Aktif Müşteriler</h3>
            <div class="value"><?= $activeCustomers ?></div>
            <div class="change">
                <i class="fas fa-user-check"></i> Son 1 yılda giriş yapanlar
            </div>
        </div>

        <div class="stat-card purple fade-in">
            <h3>Yeni Kayıtlar</h3>
            <div class="value"><?= $newCustomers ?></div>
            <div class="change">
                <i class="fas fa-user-plus"></i> Son 7 gündeki kayıtlar
            </div>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid-container">
        <!-- Yaklaşan Turlar -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-umbrella-beach"></i> Yaklaşan Turlar</h2>
                <a href="manage_tours.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Tur Adı</th>
                    <th>Tarih</th>
                    <th>Fiyat</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($upcomingTours)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px;">Yaklaşan tur bulunmamaktadır</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($upcomingTours as $tour): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($tour['image'])): ?>
                                        <img src="../assets/images/tours/<?= htmlspecialchars($tour['image']) ?>" class="tour-image" alt="<?= htmlspecialchars($tour['name']) ?>">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($tour['name']) ?></strong>
                                        <?php if ($tour['featured']): ?>
                                            <span class="badge">Öne Çıkan</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= date('d.m.Y', strtotime($tour['date'])) ?></td>
                            <td>₺<?= number_format($tour['price'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Son Müşteriler -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> Son Kayıtlı Müşteriler</h2>
                <a href="customers.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>E-posta</th>
                    <th>Kayıt Tarihi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($recentCustomers)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px;">Henüz müşteri kaydı bulunmamaktadır</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentCustomers as $customer): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="customer-avatar">
                                        <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                                    </div>
                                    <span><?= htmlspecialchars($customer['name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td><?= $customer['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kartlara hover efekti
        const cards = document.querySelectorAll('.stat-card, .card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
                card.style.boxShadow = '';
            });
        });

        // Hata mesajını 5 saniye sonra gizle
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }, 5000);
        }
    });
</script>
</body>
</html>