<?php
// admin/manage_tours.php
// pages/user/index.php
global $conn;
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/admin_functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

try {
    $query = "SELECT * FROM tours ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('<div style="background:#ffebee;padding:15px;border-left:5px solid #f44336;margin:20px;">
        <h3 style="color:#d32f2f;margin-top:0;">Sorgu Hatası</h3>
        <p><strong>Hata Mesajı:</strong> ' . $e->getMessage() . '</p>
        <p><strong>SQL Sorgusu:</strong> ' . ($e->queryString ?? 'Bilinmiyor') . '</p>
    </div>');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turları Yönet | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        :root {
            --primary-color: #FF7A00;
            --primary-hover: #e66d00;
            --secondary-color: #6c757d;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --border-color: #e0e0e0;
            --success-color: #28a745;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: var(--dark-text);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .admin-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.2);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(255, 122, 0, 0.05);
        }

        .tour-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .custom-checkbox {
            position: relative;
            padding-left: 1.75rem;
            cursor: pointer;
            user-select: none;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 1.25rem;
            width: 1.25rem;
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }

        .custom-checkbox:hover input ~ .checkmark {
            border-color: var(--primary-color);
        }

        .custom-checkbox input:checked ~ .checkmark {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .custom-checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        .custom-checkbox .checkmark:after {
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-primary {
            background-color: var(--primary-color);
        }

        .badge-secondary {
            background-color: var(--secondary-color);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:focus + .slider {
            box-shadow: 0 0 1px var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper:after {
            content: "▼";
            font-size: 0.8rem;
            color: var(--primary-color);
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        select {
            appearance: none;
            padding-right: 2.5rem;
        }

        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #f8f9fa;
            border: 1px dashed var(--border-color);
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload-btn:hover {
            background-color: rgba(255, 122, 0, 0.1);
            border-color: var(--primary-color);
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .ms-2 {
            margin-left: 0.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
        }

        .m-0 {
            margin: 0;
        }

        .p-0 {
            padding: 0;
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .admin-content {
                padding: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            table {
                font-size: 0.875rem;
            }

            th, td {
                padding: 0.75rem;
            }
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/admin_header.php'; ?>

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">Turları Yönet</h2>
        <button class="btn btn-primary" id="toggleFormBtn">
            <i class="fas fa-plus me-2"></i>Yeni Tur Ekle
        </button>
    </div>

    <!-- Tur Ekleme Formu -->
    <div class="card mb-4 fade-in" id="addTourCard" style="display:none;">
        <div class="card-header">
            <i class="fas fa-map-marked-alt me-2"></i>Yeni Tur Oluştur
        </div>
        <div class="card-body">
            <form id="addTourForm" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tourName"><i class="fas fa-heading me-2"></i>Tur Adı</label>
                            <input type="text" id="tourName" name="name" required placeholder="Tur adını giriniz">
                        </div>

                        <div class="form-group">
                            <label for="shortDescription"><i class="fas fa-align-left me-2"></i>Kısa Açıklama</label>
                            <textarea id="shortDescription" name="short_description" rows="2" required placeholder="Liste görünümünde gözükecek kısa açıklama"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tourPrice"><i class="fas fa-tag me-2"></i>Fiyat (TL)</label>
                            <input type="number" id="tourPrice" name="price" step="0.01" required placeholder="Örnek: 599.99">
                        </div>

                        <div class="form-group">
                            <label for="tourLocation"><i class="fas fa-map-marker-alt me-2"></i>Lokasyon</label>
                            <input type="text" id="tourLocation" name="location" required placeholder="Tur lokasyonu">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tourDescription"><i class="fas fa-info-circle me-2"></i>Detaylı Açıklama</label>
                            <textarea id="tourDescription" name="description" rows="4" required placeholder="Tur detaylı açıklaması"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tourType"><i class="fas fa-tags me-2"></i>Tur Türü</label>
                            <div class="select-wrapper">
                                <select id="tourType" name="type" required>
                                    <option value="" disabled selected>Tur türü seçiniz</option>
                                    <option value="cultural">Kültürel Tur</option>
                                    <option value="festival">Festival Turu</option>
                                    <option value="adaptation">Adaptasyon Turu</option>
                                    <option value="historical">Tarihi Tur</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tourDate"><i class="fas fa-calendar-alt me-2"></i>Tur Tarihi</label>
                            <input type="date" id="tourDate" name="date" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-image me-2"></i>Tur Görseli</label>
                            <div class="file-upload">
                                <label class="file-upload-btn">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>
                                    <span id="fileLabel">Dosya Seçin (800x600 px önerilir)</span>
                                    <input type="file" id="tourImage" name="image" accept="image/*">
                                </label>
                            </div>
                            <small class="text-muted">JPEG, PNG veya GIF formatında yükleyin</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-star me-2"></i>Tur Özellikleri</label>
                            <div class="d-flex align-items-center">
                                <label class="custom-checkbox me-4">
                                    Öne Çıkan Tur
                                    <input type="checkbox" name="featured">
                                    <span class="checkmark"></span>
                                </label>

                                <label class="switch me-2">
                                    <input type="checkbox" name="active" checked>
                                    <span class="slider"></span>
                                </label>
                                <span>Aktif Tur</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-secondary me-3">
                        <i class="fas fa-times me-2"></i>Temizle
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Tur Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tur Listesi -->
    <div class="card fade-in">
        <div class="card-header">
            <i class="fas fa-list me-2"></i>Tur Listesi
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="toursTable" class="mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Görsel</th>
                        <th>Tur Adı</th>
                        <th>Tür</th>
                        <th>Fiyat</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($tours)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">Henüz tur eklenmemiş</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tours as $tour): ?>
                            <tr data-id="<?= htmlspecialchars($tour['id']) ?>" class="fade-in">
                                <td><?= htmlspecialchars($tour['id']) ?></td>
                                <td>
                                    <?php if (!empty($tour['image'])): ?>
                                        <?php
                                        $imageParts = explode('_', $tour['image']);
                                        $imageFile = end($imageParts);
                                        $imagePath = '../assets/images/tours/' . $imageFile;
                                        ?>
                                        <?php if (file_exists($imagePath)): ?>
                                            <img src="<?= htmlspecialchars($imagePath) ?>"
                                                 alt="<?= htmlspecialchars($tour['name']) ?>"
                                                 class="tour-image">
                                        <?php else: ?>
                                            <div class="tour-image bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted"></i>
                                                <small>Dosya bulunamadı</small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="tour-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($tour['name']) ?></strong>
                                    <?php if (!empty($tour['featured'])): ?>
                                        <span class="badge badge-primary ms-2">Öne Çıkan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $typeLabels = [
                                        'cultural' => 'Kültürel',
                                        'festival' => 'Festival',
                                        'adaptation' => 'Adaptasyon',
                                        'historical' => 'Tarihi'
                                    ];
                                    echo $typeLabels[$tour['type']] ?? htmlspecialchars($tour['type']);
                                    ?>
                                </td>
                                <td><?= number_format($tour['price'], 2) ?> TL</td>
                                <td><?= date('d.m.Y', strtotime($tour['date'])) ?></td>
                                <td>
            <span class="badge <?= $tour['active'] ? 'badge-primary' : 'badge-secondary' ?>">
                <?= $tour['active'] ? 'Aktif' : 'Pasif' ?>
            </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-secondary btn-sm edit-btn" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-btn" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/admin.js"></script>
<script src="/assets/js/ajax.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form toggle butonu
        const toggleFormBtn = document.getElementById('toggleFormBtn');
        const addTourCard = document.getElementById('addTourCard');

        // Başlangıçta formu gizle
        addTourCard.style.display = 'none';

        toggleFormBtn.addEventListener('click', function() {
            addTourCard.style.display = addTourCard.style.display === 'none' ? 'block' : 'none';
            toggleFormBtn.innerHTML = addTourCard.style.display === 'none'
                ? '<i class="fas fa-plus me-2"></i>Yeni Tur Ekle'
                : '<i class="fas fa-minus me-2"></i>Formu Kapat';
        });

        // Dosya seçildiğinde ismi göster
        document.getElementById('tourImage').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Dosya Seçin (800x600 px önerilir)';
            document.getElementById('fileLabel').textContent = fileName;
        });

        // Tur Ekleme Formu
        document.getElementById('addTourForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formElement = this;
            const formData = new FormData(formElement);
            const submitBtn = formElement.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ekleniyor...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('../../api/add_tour.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Tur eklenirken bir hata oluştu');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: data.message || 'Tur başarıyla eklendi',
                    confirmButtonColor: '#FF7A00'
                }).then(() => {
                    window.location.reload();
                });

            } catch (error) {
                console.error('Hata:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: error.message,
                    confirmButtonColor: '#FF7A00'
                });
            } finally {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        });

        // Silme İşlemi
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tourId = this.closest('tr').dataset.id;
                const tourName = this.closest('tr').querySelector('td:nth-child(3) strong').textContent;

                Swal.fire({
                    title: 'Emin misiniz?',
                    html: `<strong>${tourName}</strong> adlı turu silmek istediğinize emin misiniz?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF7A00',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'Vazgeç'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../../api/delete_tour.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: tourId })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Silindi!',
                                        text: 'Tur başarıyla silindi.',
                                        confirmButtonColor: '#FF7A00'
                                    }).then(() => window.location.reload());
                                } else {
                                    throw new Error(data.error || 'Silme işlemi başarısız');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: error.message,
                                    confirmButtonColor: '#FF7A00'
                                });
                            });
                    }
                });
            });
        });

        // Düzenleme İşlemi
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tourId = this.closest('tr').dataset.id;
                Swal.fire({
                    title: 'Düzenleme İşlemi',
                    text: 'Bu özellik şu anda geliştirme aşamasındadır.',
                    icon: 'info',
                    confirmButtonColor: '#FF7A00'
                });
            });
        });
    });
</script>
</body>
</html>