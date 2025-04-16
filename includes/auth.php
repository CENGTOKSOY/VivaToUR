<?php
// includes/auth.php


if (!isset($_SESSION['admin_logged_in'])) {
    header('HTTP/1.0 403 Forbidden');
    die('Erişim reddedildi. Lütfen giriş yapın.');
}