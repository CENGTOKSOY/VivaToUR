<?php
global $conn;
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $newPassword = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_BCRYPT) : null;

    $query = "UPDATE users SET name = ?, phone = ?" . ($newPassword ? ", password = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($query);

    $params = [$name, $phone];
    if ($newPassword) $params[] = $newPassword;
    $params[] = $userId;

    $stmt->execute($params);
    $_SESSION['user_name'] = $name;
    $success = "Profil bilgileri güncellendi!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profilim - VivaToUR</title>
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="user-container">
    <div class="user-sidebar">
        <div class="user-avatar">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <h3><?= htmlspecialchars($user['name']) ?></h3>
        <nav>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profil</a>
            <a href="bookings.php"><i class="fas fa-suitcase"></i> Rezervasyonlar</a>
            <a href="favorites.php"><i class="fas fa-heart"></i> Favoriler</a>
            <a href="history.php"><i class="fas fa-history"></i> Geçmiş</a>
            <a href="/pages/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </nav>
    </div>

    <div class="user-content">
        <h1><i class="fas fa-user-cog"></i> Profil Ayarları</h1>
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Ad Soyad</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Telefon</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                <input type="password" name="new_password" minlength="8">
            </div>
            <button type="submit" class="btn btn-orange">Güncelle</button>
        </form>
    </div>
</div>
</body>
</html>