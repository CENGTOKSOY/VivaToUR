<?php
// admin/includes/auth.php

require_once __DIR__ . '/config.php';

/**
 * Admin girişi yapar
 * @param string $username Kullanıcı adı
 * @param string $password Şifre
 * @return bool Başarı durumu
 */
function adminLogin($username, $password) {
    // Burada gerçek bir kimlik doğrulama işlemi yapılmalı
    $validUsername = 'admin';
    $validPassword = 'password123'; // Gerçek uygulamada hashlenmiş olmalı

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }

    return false;
}

/**
 * Admin çıkış yapar
 */
function adminLogout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>