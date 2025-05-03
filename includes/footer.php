<?php
// includes/footer.php
?>
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">Hızlı Erişim</h3>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/pages/index.php">Ana Sayfa</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/tours.php">Turlar</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/about.php">Hakkımızda</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/contact.php">İletişim</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3 class="footer-title">Hesap</h3>
                    <ul class="footer-links">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="<?= BASE_URL ?>/pages/user/profile.php">Profilim</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/user/bookings.php">Rezervasyonlarım</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/auth/logout.php">Çıkış Yap</a></li>
                        <?php else: ?>
                            <li><a href="<?= BASE_URL ?>/pages/auth/login.php">Giriş Yap</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/auth/register.php">Kayıt Ol</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3 class="footer-title">İletişim</h3>
                    <ul class="footer-contact">
                        <li><i class="fas fa-map-marker-alt"></i> İstanbul, Türkiye</li>
                        <li><i class="fas fa-phone"></i> +90 555 123 45 67</li>
                        <li><i class="fas fa-envelope"></i> info@vivatour.com</li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3 class="footer-title">Bizi Takip Edin</h3>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>

                    <div class="newsletter">
                        <h4>E-Bülten Aboneliği</h4>
                        <form class="newsletter-form">
                            <input type="email" placeholder="E-posta adresiniz" required>
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> VivaToUR - Tüm Hakları Saklıdır</p>
                <div class="footer-legal">
                    <a href="<?= BASE_URL ?>/pages/auth/privacy.php">Gizlilik Politikası</a>
                    <a href="<?= BASE_URL ?>/pages/auth/terms.php">Kullanım Şartları</a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Footer Stilleri */
        .site-footer {
            background: var(--primary-dark);
            color: white;
            padding: 3rem 0 0;
            margin-top: auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-col {
            margin-bottom: 1.5rem;
        }

        .footer-title {
            color: var(--primary-light);
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: var(--primary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s, padding-left 0.3s;
            display: block;
        }

        .footer-links a:hover {
            color: var(--primary-light);
            padding-left: 5px;
        }

        .footer-contact {
            list-style: none;
        }

        .footer-contact li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-contact i {
            color: var(--primary);
            width: 20px;
            text-align: center;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .social-links a {
            color: white;
            background: rgba(255,255,255,0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .newsletter h4 {
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .newsletter-form {
            display: flex;
        }

        .newsletter-form input {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 4px 0 0 4px;
            font-size: 0.9rem;
        }

        .newsletter-form button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 1rem;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            transition: background 0.3s;
        }

        .newsletter-form button:hover {
            background: var(--primary-light);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
        }

        .footer-legal {
            display: flex;
            gap: 1.5rem;
        }

        .footer-legal a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .footer-legal a:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-bottom {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        // E-bülten formu gönderimi
        document.addEventListener('DOMContentLoaded', function() {
            const newsletterForm = document.querySelector('.newsletter-form');
            if(newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = this.querySelector('input').value;
                    // Burada AJAX ile form gönderimi yapılabilir
                    alert('Aboneliğiniz için teşekkürler!');
                    this.querySelector('input').value = '';
                });
            }
        });
    </script>

<?php
// Sayfanın geri kalanı için gerekli kapanış etiketleri
if (!defined('FOOTER_INCLUDED')) {
    define('FOOTER_INCLUDED', true);
    echo '</body></html>';
}