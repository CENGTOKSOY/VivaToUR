<?php
// includes/header.php

require_once __DIR__ . '/config.php';

// Oturum kontrolü
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı bilgileri
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Aktif sayfa
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF7A00;
            --primary-light: #FFE8D5;
            --primary-dark: #E56D00;
            --white: #FFFFFF;
            --black: #000000;
            --secondary: #333333;
            --secondary-light: #666666;
            --light: #F5F5F5;
            --danger: #E53E3E;
            --success: #38A169;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: var(--secondary);
            line-height: 1.6;
        }

        .main-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-nav {
            background-color: var(--white);
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .main-nav a {
            color: var(--secondary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .main-nav a:hover,
        .main-nav a.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .user-menu {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .user-menu a {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.3s;
        }

        .user-menu a:hover {
            opacity: 0.9;
        }

        .user-menu span {
            font-weight: 500;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .main-nav {
                flex-direction: column;
                display: none;
                padding: 1rem 0;
            }

            .main-nav.active {
                display: flex;
            }

            .user-menu {
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255,255,255,0.1);
            }

            .mobile-menu-btn {
                display: block;
                position: absolute;
                top: 1rem;
                right: 1rem;
            }
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="header-container">
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>

        <a href="<?= BASE_URL ?>pages/index.php" class="logo">
            <i class="fas fa-umbrella-beach"></i> <?= SITE_NAME ?>
        </a>

        <div class="user-menu">
            <?php if($isLoggedIn): ?>
                <span>Hoş geldiniz, <?= htmlspecialchars($userName) ?></span>
                <a href="<?= BASE_URL ?>pages/user/profile.php">
                    <i class="fas fa-user"></i> Profil
                </a>
                <a href="<?= BASE_URL ?>pages/auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Çıkış
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>pages/auth/login.php">
                    <i class="fas fa-sign-in-alt"></i> Giriş Yap
                </a>
                <a href="<?= BASE_URL ?>pages/auth/register.php">
                    <i class="fas fa-user-plus"></i> Kayıt Ol
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<nav class="main-nav" id="mainNav">
    <a href="<?= BASE_URL ?>pages/index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
        <i class="fas fa-home"></i> Ana Sayfa
    </a>
    <a href="<?= BASE_URL ?>pages/tours.php" class="<?= $currentPage === 'tours.php' ? 'active' : '' ?>">
        <i class="fas fa-umbrella-beach"></i> Turlar
    </a>
    <a href="<?= BASE_URL ?>pages/contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">
        <i class="fas fa-envelope"></i> İletişim
    </a>
    <a href="<?= BASE_URL ?>pages/about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">
        <i class="fas fa-info-circle"></i> Hakkımızda
    </a>
</nav>

<script>
    // Mobil menü toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mainNav = document.getElementById('mainNav');

        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });

        // Ekran boyutu değiştiğinde kontrol et
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                mainNav.classList.remove('active');
            }
        });
    });
</script>