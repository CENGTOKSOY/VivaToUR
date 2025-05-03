<?php
// includes/config.php

// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');
define('DB_NAME', 'vivatour_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// BASE_URL tanımı (PROJE KÖKÜ)
define('BASE_URL', '/VivaToUR/');

// Diğer sabitler
define('SITE_NAME', 'VivaToUR');
define('DEFAULT_TIMEZONE', 'Europe/Istanbul');

// Oturum ayarları
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Güvenlik başlıkları
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Zaman dilimi
date_default_timezone_set(DEFAULT_TIMEZONE);