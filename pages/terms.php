<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanım Koşulları | VivaToUR</title>
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

        p, ul, ol {
            margin-bottom: 1.2rem;
            font-size: 1.05rem;
            color: #555;
        }

        ul, ol {
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        .highlight {
            background-color: var(--primary-light);
            padding: 2px 5px;
            border-radius: 3px;
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
        <h1><i class="fas fa-file-contract"></i> Kullanım Koşulları</h1>
        <p>VivaToUR hizmetlerini kullanmadan önce lütfen okuyunuz</p>
        <span class="last-updated">Son güncellenme: <?= date('d.m.Y') ?></span>
    </div>
</header>

<div class="legal-container">
    <div class="legal-card">
        <h2>1. Genel Hükümler</h2>
        <p>Bu platform <span class="highlight">VivaToUR Turizm A.Ş.</span> ("Şirket") tarafından işletilmektedir. Siteyi kullanarak bu koşulları kabul etmiş sayılırsınız.</p>

        <h2>2. Hizmetler</h2>
        <p>VivaToUR aşağıdaki hizmetleri sunar:</p>
        <ul>
            <li>Kültür turları rezervasyonu</li>
            <li>Tur bilgilerinin sunulması</li>
            <li>Online ödeme işlemleri</li>
            <li>Müşteri destek hizmetleri</li>
        </ul>

        <h2>3. Kullanıcı Yükümlülükleri</h2>
        <p>Kullanıcıların aşağıdaki yükümlülükleri vardır:</p>
        <ol>
            <li>Doğru ve güncel bilgi sağlamak</li>
            <li>Yetkisiz erişim girişiminde bulunmamak</li>
            <li>Sistemi kötüye kullanmamak</li>
            <li>Telif haklarına saygı göstermek</li>
        </ol>

        <h2>4. Rezervasyon ve İptal Politikası</h2>
        <h3>4.1 Rezervasyon</h3>
        <p>Rezervasyonlar ödeme alındıktan sonra kesinleşir. Tur fiyatları vergiler dahildir.</p>

        <h3>4.2 İptal Koşulları</h3>
        <ul>
            <li>Tur tarihinden <strong>15 gün önce</strong>: %100 iade</li>
            <li>Tur tarihinden <strong>7-14 gün önce</strong>: %50 iade</li>
            <li>Tur tarihinden <strong>7 gün içinde</strong>: İade yoktur</li>
        </ul>

        <h2>5. Sorumluluk Sınırlaması</h2>
        <p>Şirket, aşağıdaki durumlardan sorumlu değildir:</p>
        <ul>
            <li>Doğal afetler nedeniyle iptal edilen turlar</li>
            <li>Kullanıcı hatalarından kaynaklanan kayıplar</li>
            <li>Üçüncü taraf hizmetlerindeki aksaklıklar</li>
        </ul>

        <h2>6. Fikri Mülkiyet</h2>
        <p>Sitedeki tüm içerik (metin, görsel, logo) Şirket'in mülkiyetindedir. İzinsiz kullanımı yasaktır.</p>

        <h2>7. Değişiklikler</h2>
        <p>Şirket, önceden haber vermeksizin bu koşulları değiştirme hakkını saklı tutar.</p>

        <h2>8. Uygulanacak Hukuk</h2>
        <p>Bu sözleşme Türkiye Cumhuriyeti hukukuna tabidir. İstanbul Mahkemeleri yetkilidir.</p>

        <a href="/pages/auth/register.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kayıt Sayfasına Dön
        </a>
    </div>
</div>
</body>
</html>