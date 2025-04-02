<div class="user-sidebar">
    <div class="user-avatar">
        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
    </div>
    <h3><?= htmlspecialchars($_SESSION['user_name']) ?></h3>
    <nav>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="bookings.php"><i class="fas fa-suitcase"></i> Rezervasyonlar</a>
        <a href="favorites.php" class="active"><i class="fas fa-heart"></i> Favoriler</a>
        <a href="history.php"><i class="fas fa-history"></i> Geçmiş</a>
        <a href="/pages/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
    </nav>
</div>