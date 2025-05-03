<?php
// pages/auth/logout.php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

// Oturumu sonlandır
session_unset();
session_destroy();

// HTML ile çıkış sayfası
$pageTitle = "Çıkış Yapılıyor";
include __DIR__ . '/../../includes/header.php';
?>

    <div class="logout-container" style="
    max-width: 600px;
    margin: 5rem auto;
    padding: 2rem;
    text-align: center;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
">
        <div class="logout-icon" style="
        font-size: 4rem;
        color: var(--primary);
        margin-bottom: 1.5rem;
    ">
            <i class="fas fa-sign-out-alt"></i>
        </div>

        <h1 style="color: var(--primary); margin-bottom: 1rem;">Başarıyla Çıkış Yaptınız</h1>
        <p style="margin-bottom: 2rem; color: var(--secondary);">Güvenli çıkış işlemi tamamlandı. Yine bekleriz!</p>

        <a href="<?= BASE_URL ?>/pages/index.php" class="btn" style="
        display: inline-block;
        padding: 0.8rem 1.5rem;
        background: var(--primary);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s;
    ">
            <i class="fas fa-home"></i> Ana Sayfaya Dön
        </a>
    </div>

    <script>
        // 3 saniye sonra otomatik yönlendirme
        setTimeout(function() {
            window.location.href = "<?= BASE_URL ?>/pages/index.php";
        }, 3000);
    </script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>