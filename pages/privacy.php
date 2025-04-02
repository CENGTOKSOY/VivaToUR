<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gizlilik Politikası | VivaToUR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF7A00;
            --primary-light: #FFE8D5;
            --dark: #2D3748;
            --light: #F7FAFC;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        body {
            background-color: #f9f9f9;
            color: #333;
        }

        .legal-header {
            background: linear-gradient(135deg, var(--primary), #FF9A3E);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
            margin-bottom: 40px;
        }

        .legal-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        .legal-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.06);
            padding: 40px;
            margin-top: -40px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        h2 {
            color: var(--primary);
            margin: 2.5rem 0 1rem;
            font-size: 1.5rem;
        }

        h3 {
            margin: 1.8rem 0 0.8rem;
            font-size: 1.2rem;
        }

        p, ul {
            margin-bottom: 1.2rem;
            font-size: 1.05rem;
            color: #555;
        }

        ul {
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        .last-updated {
            color: #777;
            font-style: italic;
            margin-bottom: 2rem;
            display: block;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-top: 2rem;
        }

        .back-btn i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .legal-header {
                padding: 60px 20px 40px;
            }

            .legal-card {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<header class="legal-header">
    <div class="legal-container">
        <h1><i class="fas fa-shield-alt"></i> Gizlilik Politikası</h1>
        <p>Kişisel verilerinizin güvenliği bizim için önemlidir</p>
        <span class="last-updated">Son güncellenme: <?= date('d.m.Y') ?></span>
    </div>
</header>

<div class="legal-container">
    <div class="legal-card">
        <h2>1. Veri Toplama</h2>
        <p>VivaToUR olarak, hizmetlerimizi sunarken şu kişisel verileri topluyoruz:</p>
        <ul>
            <li><strong>Kimlik bilgileri:</strong> Ad-soyad, e-posta, telefon numarası</li>
            <li><strong>Demografik bilgiler:</strong> Doğum tarihi, cinsiyet (isteğe bağlı)</li>
            <li><strong>Ödeme bilgileri:</strong> Şifrelenmiş kredi kartı bilgileri</li>
            <li><strong>Kullanım verileri:</strong> IP adresi, tarayıcı bilgisi, sayfa görüntüleme süreleri</li>
        </ul>

        <h2>2. Veri Kullanımı</h2>
        <p>Topladığımız verileri şu amaçlarla kullanıyoruz:</p>
        <ul>
            <li>Hesap oluşturma ve kimlik doğrulama</li>
            <li>Rezervasyon işlemlerini gerçekleştirme</li>
            <li>Müşteri hizmetleri sunma</li>
            <li>Yasal yükümlülükleri yerine getirme</li>
            <li>Hizmet iyileştirme çalışmaları</li>
        </ul>

        <h2>3. Veri Paylaşımı</h2>
        <p>Verileriniz yalnızca:</p>
        <ul>
            <li>Yasal zorunluluklar gereği</li>
            <li>Tur operatörleriyle sınırlı bilgi paylaşımı</li>
            <li>Ödeme işlem ortaklarıyla güvenli şekilde paylaşılır</li>
        </ul>

        <h2>4. Çerez Politikası</h2>
        <p>Sitemizde kullanıcı deneyimini geliştirmek için çerezler kullanıyoruz:</p>
        <ul>
            <li><strong>Zorunlu çerezler:</strong> Site fonksiyonları için gerekli</li>
            <li><strong>Analiz çerezleri:</strong> Kullanım istatistikleri için</li>
            <li><strong>Tercih çerezleri:</strong> Kullanıcı ayarlarını hatırlamak için</li>
        </ul>

        <h2>5. Haklarınız</h2>
        <p>KVKK kapsamında şu haklara sahipsiniz:</p>
        <ul>
            <li>Verilerinize erişim</li>
            <li>Düzeltme talep etme</li>
            <li>Silinmesini isteme</li>
            <li>İşleme itiraz hakkı</li>
        </ul>

        <p>Haklarınızı kullanmak için <a href="mailto:kvkk@vivatur.com">kvkk@vivatur.com</a> adresine e-posta gönderebilirsiniz.</p>

        <a href="auth/register.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kayıt Sayfasına Dön</a>
    </div>
</div>
</body>
</html>