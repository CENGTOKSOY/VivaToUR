<?php
// admin/customers.php

// 1. Oturum ve güvenlik kontrolleri
global $conn;
session_start();
ob_start();

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/admin_functions.php';

// Giriş kontrolü - Tüm admin sayfalarında bu 3 satır olmalı
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
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

// 3. Müşteri verilerini al
try {
    $search = $_GET['search'] ?? '';
    $query = "SELECT id, name, email, phone, birth_date, 
                     to_char(created_at, 'DD.MM.YYYY') as created_at,
                     to_char(last_login, 'DD.MM.YYYY HH24:MI') as last_login
              FROM users";

    if (!empty($search)) {
        $query .= " WHERE name ILIKE :search OR email ILIKE :search";
        $stmt = $conn->prepare($query);
        $stmt->execute([':search' => "%$search%"]);
    } else {
        $stmt = $conn->query($query);
    }

    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Müşteri Sorgu Hatası: " . $e->getMessage());
    die('<div class="error-alert">Müşteri verileri yüklenirken bir hata oluştu</div>');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteriler | VivaToUR Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            background-color: var(--light-bg);
            color: var(--dark-text);
            margin: 0;
        }

        /* Sidebar */
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

        /* Main Content */
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

        /* Cards */
        .card {
            background: white;
            border-radius: 10px;
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
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            margin: 0;
            color: var(--primary-color);
            font-size: 20px;
        }

        /* Search Box */
        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 30px;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.2);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(255, 122, 0, 0.05);
        }

        /* Customer Avatar */
        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        /* Buttons */
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

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background: var(--primary-color);
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            padding: 20px;
            list-style: none;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination a {
            display: block;
            padding: 8px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--dark-text);
            text-decoration: none;
            transition: all 0.3s;
        }

        .pagination a:hover, .pagination .active a {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Error Alert */
        .error-alert {
            background: #ffebee;
            color: #d32f2f;
            padding: 15px;
            border-radius: 5px;
            margin: 20px;
            border-left: 5px solid #f44336;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
        }

        @media (max-width: 768px) {
            .search-box {
                width: 100%;
                margin-top: 15px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-info {
                margin-top: 15px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 15px;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
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
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="manage_tours.php"><i class="fas fa-umbrella-beach"></i> Turları Yönet</a></li>
        <li><a href="customers.php" class="active"><i class="fas fa-users"></i> Müşteriler</a></li>
        <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Rezervasyonlar</a></li>
        <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1><i class="fas fa-users"></i> Müşteriler</h1>
        <div class="user-info">
            <img src="../assets/images/admin-avatar.png" alt="Admin">
            <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
            <div class="user-dropdown">
                <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a>
                <a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Müşteri Listesi</h2>
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Müşteri ara..."
                       value="<?= htmlspecialchars($search ?? '') ?>">
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Müşteri</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Doğum Tarihi</th>
                    <th>Son Giriş</th>
                    <th>İşlemler</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">
                            <i class="fas fa-info-circle" style="color: var(--secondary-color);"></i>
                            Kayıtlı müşteri bulunamadı
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= $customer['id'] ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="customer-avatar">
                                        <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                                    </div>
                                    <span><?= htmlspecialchars($customer['name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td><?= $customer['phone'] ? htmlspecialchars($customer['phone']) : '<span style="color:var(--secondary-color);">Belirtilmemiş</span>' ?></td>
                            <td><?= $customer['birth_date'] ? date('d.m.Y', strtotime($customer['birth_date'])) : '-' ?></td>
                            <td><?= $customer['last_login'] ?? '<span style="color:var(--secondary-color);">Hiç giriş yapmamış</span>' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary btn-sm btn-view" data-id="<?= $customer['id'] ?>">
                                        <i class="fas fa-eye"></i> Detay
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $customer['id'] ?>">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <ul class="pagination">
                <li class="active"><a href="?page=1">1</a></li>
                <li><a href="?page=2">2</a></li>
                <li><a href="?page=3">3</a></li>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Müşteri silme işlemi
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                const customerName = this.closest('tr').querySelector('td:nth-child(2) span').textContent;

                Swal.fire({
                    title: 'Emin misiniz?',
                    html: `<strong>${customerName}</strong> adlı müşteriyi silmek istediğinize emin misiniz?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                    cancelButtonColor: 'var(--secondary-color)',
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'Vazgeç'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../../api/delete_customer.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: customerId })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Silindi!',
                                        text: 'Müşteri başarıyla silindi.',
                                        confirmButtonColor: 'var(--primary-color)'
                                    }).then(() => window.location.reload());
                                } else {
                                    throw new Error(data.message || 'Silme işlemi başarısız');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: error.message,
                                    confirmButtonColor: 'var(--primary-color)'
                                });
                            });
                    }
                });
            });
        });

        // Müşteri detay görüntüleme
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                // AJAX ile müşteri detaylarını getir
                fetch(`../../api/get_customer.php?id=${customerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: data.data.name,
                                html: `
                                <div style="text-align:left;margin-top:20px;">
                                    <p><strong>E-posta:</strong> ${data.data.email}</p>
                                    <p><strong>Telefon:</strong> ${data.data.phone || 'Belirtilmemiş'}</p>
                                    <p><strong>Kayıt Tarihi:</strong> ${data.data.created_at}</p>
                                    <p><strong>Son Giriş:</strong> ${data.data.last_login || 'Hiç giriş yapmamış'}</p>
                                </div>
                            `,
                                confirmButtonColor: 'var(--primary-color)'
                            });
                        } else {
                            throw new Error(data.message || 'Müşteri bilgileri alınamadı');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: error.message,
                            confirmButtonColor: 'var(--primary-color)'
                        });
                    });
            });
        });

        // Arama kutusu otomatik submit
        document.querySelector('.search-box input').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    });
</script>
</body>
</html>