<?php
// about.php - TAM ÇALIŞAN KOD
require_once __DIR__ . '/../admin/includes/config.php'; // PostgreSQL bağlantısı için
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VivaToUR - Hakkımızda</title>
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
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--viva-orange), #FF9A3E);
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(255, 122, 0, 0.2);
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        nav {
            background-color: var(--viva-white);
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav a {
            color: var(--viva-dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }

        nav a:hover, nav a.active {
            background-color: var(--viva-orange-light);
            color: var(--viva-orange);
        }

        .container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .about-hero {
            background: url('/assets/images/about-bg.jpg') no-repeat center center;
            background-size: cover;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 122, 0, 0.7);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
            text-align: center;
            padding: 2rem;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .mission-card, .vision-card {
            background: var(--viva-white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 4px solid var(--viva-orange);
        }

        .mission-card h2, .vision-card h2 {
            color: var(--viva-orange);
            margin-top: 0;
        }

        .team-section h2 {
            text-align: center;
            color: var(--viva-orange);
            margin-bottom: 2rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .team-member {
            background: var(--viva-white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .member-img {
            height: 250px;
            background-size: cover;
            background-position: center;
        }

        .member-info {
            padding: 1.5rem;
            text-align: center;
        }

        .member-info h3 {
            margin: 0 0 0.5rem;
            color: var(--viva-orange);
        }

        .member-role {
            color: #666;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-links a {
            color: var(--viva-orange);
            font-size: 1.2rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--viva-orange), #FF9A3E);
            color: white;
            padding: 3rem;
            text-align: center;
            border-radius: 15px;
            margin-top: 3rem;
        }

        .cta-section h2 {
            margin-top: 0;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: var(--viva-orange);
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        footer {
            background: var(--viva-dark);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 0.5rem;
                align-items: center;
            }

            .hero-content h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">VivaToUR</div>
    <p>Kültür Köprüsü Turizm</p>
</header>

<nav>
    <a href="/pages/index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="/pages/tours.php"><i class="fas fa-umbrella-beach"></i> Turlar</a>
    <a href="/pages/contact.php"><i class="fas fa-envelope"></i> İletişim</a>
    <a href="/pages/about.php" class="active"><i class="fas fa-info-circle"></i> Hakkımızda</a>
</nav>

<div class="container">
    <section class="about-hero">
        <div class="hero-content">
            <h1>Kültürlerarası Köprü Kuruyoruz</h1>
            <p>Türkiye'nin renklerini dünyaya, dünyanın renklerini Türkiye'ye taşıyoruz</p>
        </div>
    </section>

    <section>
        <h2 style="color: var(--viva-orange); text-align: center; margin-bottom: 2rem;">Biz Kimiz?</h2>
        <p style="text-align: center; max-width: 800px; margin: 0 auto 3rem;">
            VivaToUR, 2025 yılında Zonguldak Bülent Ecevit Üniversitesi Bilgisayar Mühendisliği bölümü 3. sınıf öğrencileri tarafından kurulmuş bir kültür turizmi girişimidir. Amacımız, Türkiye'nin zengin kültürel mirasını hem yerli hem de yabancı misafirlerimize en otantik şekilde sunarken, kültürlerarası etkileşimi artırmaktır.
        </p>
    </section>

    <section class="mission-vision">
        <div class="mission-card">
            <h2><i class="fas fa-bullseye"></i> Misyonumuz</h2>
            <p>Yerel kültürleri koruyarak turizme kazandırmak, otantik deneyimler sunmak ve kültürlerarası diyaloğu güçlendirmek. Türkiye'de yaşayan yabancıların kültürel adaptasyon sürecine destek olarak, toplumsal uyumu kolaylaştırıyoruz.</p>
        </div>

        <div class="vision-card">
            <h2><i class="fas fa-eye"></i> Vizyonumuz</h2>
            <p>2028 yılına kadar Türkiye'nin önde gelen kültür ve adaptasyon turizmi platformu olmak. Her yıl 10.000'den fazla misafire unutulmaz kültürel deneyimler sunmayı hedefliyoruz.</p>
        </div>
    </section>

    <section class="team-section">
        <h2><i class="fas fa-users"></i> Ekibimiz</h2>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-img" style="background-image: url('/assets/images/team1.jpg');"></div>
                <div class="member-info">
                    <h3>Ali Gaffar Toksoy</h3>
                    <p class="member-role">Kurucu & CEO</p>
                    <p>10 yıllık turizm deneyimiyle ekibimize liderlik ediyor.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="member-img" style="background-image: url('/assets/images/team2.jpg');"></div>
                <div class="member-info">
                    <h3>Hüseyin Demirel</h3>
                    <p class="member-role">Kurumsal İlişkiler Müdürü</p>
                    <p>Kültür turları uzmanı ve profesyonel rehber.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="member-img" style="background-image: url('/assets/images/team3.jpg');"></div>
                <div class="member-info">
                    <h3>Hasan Hüseyin Kurt</h3>
                    <p class="member-role">Marketing Manager</p>
                    <p>Kültür turlarını tüm dünyaya yayma ve duyurma liderimiz.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="member-img" style="background-image: url('/assets/images/team4.jpg');"></div>
                <div class="member-info">
                    <h3>Şükran Kurt</h3>
                    <p class="member-role">Müşteri İlişkileri</p>
                    <p>5 dil bilen ekibimizin uluslararası yüzü.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-skype"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2>Kültür Yolculuğuna Hazır mısınız?</h2>
        <p>Şimdi keşfedin, unutulmaz anılar biriktirin!</p>
        <a href="/pages/tours.php" class="cta-button">Turları Görüntüle</a>
    </section>
</div>

<footer>
    <p>&copy; 2025 VivaToUR - Tüm Hakları Saklıdır</p>
    <div style="margin-top: 1rem;">
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-facebook"></i></a>
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-instagram"></i></a>
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-twitter"></i></a>
        <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-whatsapp"></i></a>
    </div>
</footer>

<script src="/assets/js/script.js"></script>
</body>
</html>