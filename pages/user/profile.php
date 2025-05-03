<?php
// pages/user/profile.php

global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/auth/login.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: ' . BASE_URL . '/pages/auth/login.php');
    exit;
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($name)) {
        $error = "Ad soyad alanı boş bırakılamaz";
    } elseif (strlen($name) < 3) {
        $error = "Ad soyad en az 3 karakter olmalıdır";
    }

    if (empty($error) && !empty($newPassword)) {
        if (empty($currentPassword)) {
            $error = "Mevcut şifrenizi giriniz";
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $error = "Mevcut şifreniz hatalı";
        } elseif (strlen($newPassword) < 8) {
            $error = "Yeni şifre en az 8 karakter olmalıdır";
        }
    }

    if (empty($error)) {
        try {
            $query = "UPDATE users SET name = ?, phone = ?";
            $params = [$name, $phone];

            if (!empty($newPassword)) {
                $query .= ", password = ?";
                $params[] = password_hash($newPassword, PASSWORD_BCRYPT);
            }

            $query .= " WHERE id = ?";
            $params[] = $_SESSION['user_id'];

            $stmt = $conn->prepare($query);
            $stmt->execute($params);

            $_SESSION['user_name'] = $name;
            $success = "Profil bilgileriniz başarıyla güncellendi!";

        } catch (PDOException $e) {
            $error = "Güncelleme sırasında bir hata oluştu: " . $e->getMessage();
        }
    }
}

$pageTitle = "Profilim";
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="profile-page-container">
        <div class="profile-sidebar">
            <div class="user-avatar-large">
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
            <h3><?= htmlspecialchars($_SESSION['user_name']) ?></h3>

            <nav class="sidebar-nav">
                <a href="<?= BASE_URL ?>/pages/user/profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i> Profil Bilgileri
                </a>
                <a href="<?= BASE_URL ?>/pages/user/bookings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'bookings.php' ? 'active' : '' ?>">
                    <i class="fas fa-suitcase"></i> Rezervasyonlarım
                </a>
                <a href="<?= BASE_URL ?>/pages/user/favorites.php" class="<?= basename($_SERVER['PHP_SELF']) === 'favorites.php' ? 'active' : '' ?>">
                    <i class="fas fa-heart"></i> Favorilerim
                </a>
                <a href="<?= BASE_URL ?>/pages/user/history.php" class="<?= basename($_SERVER['PHP_SELF']) === 'history.php' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Geçmiş Turlar
                </a>
                <a href="<?= BASE_URL ?>/pages/index.php">
                    <i class="fas fa-home"></i> Ana Sayfa
                </a>
                <a href="<?= BASE_URL ?>/pages/auth/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </nav>
        </div>

        <div class="profile-content">
            <h1><i class="fas fa-user-cog"></i> Profil Ayarları</h1>

            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="name">Ad Soyad</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="phone">Telefon</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="current_password">Mevcut Şifre (Değişiklik için)</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">Yeni Şifre</label>
                    <input type="password" id="new_password" name="new_password">
                    <small class="form-text">Değiştirmek istemiyorsanız boş bırakın</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                    <a href="<?= BASE_URL ?>/pages/index.php" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Ana Sayfa
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .profile-page-container {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
            gap: 2rem;
        }

        .profile-sidebar {
            width: 250px;
            background: var(--white);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .user-avatar-large {
            width: 100px;
            height: 100px;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }

        .profile-sidebar h3 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-nav a {
            padding: 0.8rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--secondary);
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .logout-btn {
            color: var(--danger);
            margin-top: 1rem;
        }

        .logout-btn:hover {
            background-color: #fde8e8;
        }

        .profile-content {
            flex: 1;
            background: var(--white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .profile-content h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-form .form-group {
            margin-bottom: 1.5rem;
        }

        .profile-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary);
        }

        .profile-form input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .profile-form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-text {
            display: block;
            margin-top: 0.25rem;
            color: var(--secondary-light);
            font-size: 0.8rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--light);
            color: var(--secondary);
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-danger {
            background-color: #fde8e8;
            color: var(--danger);
        }

        .alert-success {
            background-color: #e8f8ef;
            color: var(--success);
        }

        @media (max-width: 768px) {
            .profile-page-container {
                flex-direction: column;
            }

            .profile-sidebar {
                width: 100%;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>