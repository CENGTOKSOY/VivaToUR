<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş kontrolü (admin değil normal kullanıcı için)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    die('Erişim reddedildi. Lütfen giriş yapın.');
}