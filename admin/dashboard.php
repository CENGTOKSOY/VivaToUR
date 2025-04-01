<?php
// admin/dashboard.php
require_once 'includes/auth.php';
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
            --viva-orange: #FF7A00;
            --viva-orange-light: #FFE8D5;
            --viva-dark: #333333;
            --viva-gray: #F5F5F5;
            --viva-white: #FFFFFF;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: var(--viva-gray);
            color: var(--viva-dark);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: var(--viva-white);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            padding-top: 20px;
        }

        .logo {
            text-align: center;
            padding: 20px 0 40px;
        }

        .logo img {
            width: 150px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-menu li a {
            display: block;
            padding: 15px 30px;
            color: var(--viva-dark);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-menu li a:hover, .nav-menu li a.active {
            background: var(--viva-orange-light);
            color: var(--viva-orange);
            border-left: 4px solid var(--viva-orange);
        }

        .nav-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: var(--viva-orange);
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--viva-white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(255, 122, 0, 0.1);
        }

        .stat-card.orange {
            border-top: 4px solid var(--viva-orange);
        }

        .stat-card h3 {
            margin-top: 0;
            color: var(--viva-orange);
            font-size: 14px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
        }

        .stat-card .change {
            color: #4CAF50;
            font-size: 14px;
        }

        /* Recent Tours */
        .card {
            background: var(--viva-white);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            margin: 0;
            color: var(--viva-orange);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            padding: 12px 15px;
            background: var(--viva-orange-light);
            color: var(--viva-orange);
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status.active {
            background: #E8F5E9;
            color: #4CAF50;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-orange {
            background: var(--viva-orange);
            color: white;
        }

        .btn-orange:hover {
            background: #E56D00;
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
        <li><a href="#"><i class="fas fa-users"></i> Müşteriler</a></li>
        <li><a href="#"><i class="fas fa-chart-line"></i> Raporlar</a></li>
        <li><a href="#"><i class="fas fa-cog"></i> Ayarlar</a></li>
        <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>Dashboard</h1>
        <div class="user-info">
            <img src="../assets/images/admin-avatar.png" alt="Admin">
            <span>Admin</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card orange">
            <h3>Toplam Tur</h3>
            <div class="value">24</div>
            <div class="change">+2 bu ay</div>
        </div>

        <div class="stat-card">
            <h3>Rezervasyonlar</h3>
            <div class="value">156</div>
            <div class="change">+15% geçen aya göre</div>
        </div>

        <div class="stat-card">
            <h3>Toplam Gelir</h3>
            <div class="value">₺84,500</div>
            <div class="change">+22% geçen aya göre</div>
        </div>

        <div class="stat-card">
            <h3>Yeni Müşteriler</h3>
            <div class="value">42</div>
            <div class="change">+8 bu ay</div>
        </div>
    </div>

    <!-- Recent Tours -->
    <div class="card">
        <div class="card-header">
            <h2>Son Eklenen Turlar</h2>
            <button class="btn btn-orange">Tümünü Gör</button>
        </div>

        <table>
            <thead>
            <tr>
                <th>Tur Adı</th>
                <th>Fiyat</th>
                <th>Rezervasyon</th>
                <th>Durum</th>
                <th>İşlem</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Kapadokya Balon Turu</td>
                <td>₺1,500</td>
                <td>24</td>
                <td><span class="status active">Aktif</span></td>
                <td><button class="btn btn-orange">Düzenle</button></td>
            </tr>
            <tr>
                <td>Ege Yat Turu</td>
                <td>₺2,300</td>
                <td>18</td>
                <td><span class="status active">Aktif</span></td>
                <td><button class="btn btn-orange">Düzenle</button></td>
            </tr>
            <tr>
                <td>İstanbul Kültür Turu</td>
                <td>₺750</td>
                <td>32</td>
                <td><span class="status active">Aktif</span></td>
                <td><button class="btn btn-orange">Düzenle</button></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
</body>
</html>