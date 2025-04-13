<?php
require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../admin/includes/admin_functions.php'; // db.php'yi direkt require etmeyin

$tours = getTours(); // admin_functions.php'deki fonksiyon kullanılacak
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VivaToUR - Turlarımız</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
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
        }

        header {
            background: linear-gradient(135deg, var(--viva-orange), #FF9A3E);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
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
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--viva-orange);
        }

        .tour-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .tour-card {
            background: var(--viva-white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .tour-card:hover {
            transform: translateY(-5px);
        }

        .tour-image {
            width: 300px;
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .tour-price {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--viva-orange);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }

        .tour-content {
            padding: 1.5rem;
        }

        .tour-title {
            margin-top: 0;
            color: var(--viva-orange);
        }

        .tour-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
        }

        .btn-call {
            background: var(--viva-orange);
            color: white;
        }

        .filter-section {
            background: var(--viva-white);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        select, input {
            padding: 0.6rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }

        footer {
            background: var(--viva-dark);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">VivaToUR</div>
    <p>Kültür Köprüsü Turizm</p>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="tours.php" class="active"><i class="fas fa-umbrella-beach"></i> Turlar</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> İletişim</a>
    <a href="about.php"><i class="fas fa-info-circle"></i> Hakkımızda</a>
</nav>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-umbrella-beach"></i> Keşfedilecek Turlar</h1>
        <p>Kültürümüzü deneyimleyin, unutulmaz anılar biriktirin</p>
    </div>

    <div class="filter-section">
        <h3><i class="fas fa-filter"></i> Filtrele</h3>
        <div class="filter-grid">
            <div>
                <label for="tour-type">Tur Türü</label>
                <select id="tour-type">
                    <option value="">Hepsi</option>
                    <option value="cultural">Kültürel Turlar</option>
                    <option value="festival">Festival Turları</option>
                    <option value="adaptation">Adaptasyon Turları</option>
                </select>
            </div>
            <div>
                <label for="city">Şehir</label>
                <select id="city">
                    <option value="">Hepsi</option>
                    <option value="istanbul">İstanbul</option>
                    <option value="kapadokya">Kapadokya</option>
                    <option value="ephesus">Efes</option>
                </select>
            </div>
            <div>
                <label for="price-range">Fiyat Aralığı</label>
                <select id="price-range">
                    <option value="">Hepsi</option>
                    <option value="0-1000">0-1000 TL</option>
                    <option value="1000-2000">1000-2000 TL</option>
                    <option value="2000+">2000+ TL</option>
                </select>
            </div>
            <div>
                <label for="date">Tarih</label>
                <input type="date" id="date">
            </div>
        </div>
    </div>

    <div class="tour-grid" id="tour-container">
        <?php
        $tours = getTours();
        foreach ($tours as $tour):
            $image = !empty($tour['image']) ? $tour['image'] : '../assets/images/tour-default.jpg';
            ?>
            <div class="tour-card">
                <div class="tour-image" style="background-image: url('<?= $image ?>');">
                    <span class="tour-price"><?= $tour['price'] ?> TL</span>
                </div>
                <div class="tour-content">
                    <h3 class="tour-title"><?= htmlspecialchars($tour['name']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
                    <div class="tour-meta">
                        <p><i class="fas fa-map-marker-alt"></i> <?= $tour['location'] ?></p>
                        <p><i class="fas fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($tour['date'])) ?></p>
                    </div>
                    <div class="tour-actions">
                        <a href="https://wa.me/905551234567?text=<?= urlencode($tour['name'] . ' turu hakkında bilgi almak istiyorum') ?>"
                           class="btn btn-whatsapp">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="tel:+905551234567" class="btn btn-call">
                            <i class="fas fa-phone"></i> Ara
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 VivaToUR - Tüm Hakları Saklıdır</p>
    <div class="social-links">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
</footer>

<script src="../assets/js/ajax.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... diğer kodlar ...

        // Tur Ekleme Formu
        document.getElementById('addTourForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Tüm gerekli alanları kontrol et
            const requiredFields = ['name', 'short_description', 'price', 'location', 'description', 'type', 'date'];
            let missingFields = [];

            requiredFields.forEach(field => {
                const element = document.querySelector(`[name="${field}"]`);
                if (!element.value.trim()) {
                    missingFields.push(field);
                    element.style.borderColor = 'red';
                } else {
                    element.style.borderColor = '';
                }
            });

            if (missingFields.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Eksik Bilgi!',
                    html: `Lütfen aşağıdaki alanları doldurun:<br><strong>${missingFields.join(', ')}</strong>`,
                    confirmButtonColor: '#FF7A00'
                });
                return;
            }

            // Tarih formatını kontrol et
            const tourDate = new Date(document.getElementById('tourDate').value);
            if (isNaN(tourDate.getTime())) {
                Swal.fire({
                    icon: 'error',
                    title: 'Geçersiz Tarih!',
                    text: 'Lütfen geçerli bir tarih seçin.',
                    confirmButtonColor: '#FF7A00'
                });
                return;
            }

            // Form verilerini oluştur
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Butonu yükleme durumuna getir
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ekleniyor...';
            submitBtn.disabled = true;

            // Form verilerini konsola yazdır (debug için)
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('../../api/add_tour.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Tur başarıyla eklendi.',
                            confirmButtonColor: '#FF7A00'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.error || 'Tur eklenirken bir hata oluştu.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        html: `İstek gönderilirken bir hata oluştu:<br><strong>${error.message}</strong>`,
                        confirmButtonColor: '#FF7A00'
                    });
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // ... diğer kodlar ...
    });
</script>
</body>
</html>