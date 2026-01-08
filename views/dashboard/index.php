<?php
require_once __DIR__ . '/../../config/app.php';
use Services\{AuthService, StatisticsService};
use Repositories\FavoriteRepository;

$authService = new AuthService();
$currentUser = $authService->requireAuth();
$statsService = new StatisticsService();
$favoriteRepo = new FavoriteRepository();

// Get stats based on user role
if ($currentUser->isAdmin()) {
    $stats = $statsService->getDashboardStats();
} elseif ($currentUser->isHost()) {
    $userStats = $statsService->getUserStats($currentUser->getId());
    $hostStats = $statsService->getHostStats($currentUser->getId());
} else {
    $userStats = $statsService->getUserStats($currentUser->getId());
    $favoriteCount = $favoriteRepo->countByUser($currentUser->getId());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RentHub</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;}
        body {font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f5f5f5;}
        .navbar {background:white; box-shadow:0 2px 10px rgba(0,0,0,0.1); padding:15px 0;}
        .nav-container {max-width:1200px; margin:0 auto; padding:0 20px; display:flex; justify-content:space-between; align-items:center;}
        .logo {font-size:1.5em; font-weight:bold; color:#667eea; text-decoration:none;}
        .nav-links a {margin-left:20px; color:#333; text-decoration:none; font-weight:500;}
        .container {max-width:1200px; margin:40px auto; padding:0 20px;}
        .welcome {background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:40px; border-radius:15px; margin-bottom:30px;}
        .welcome h1 {font-size:2.5em; margin-bottom:10px;}
        .welcome p {font-size:1.2em; opacity:0.9;}
        .stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:20px; margin-bottom:30px;}
        .stat-card {background:white; padding:30px; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.1);}
        .stat-icon {font-size:3em; margin-bottom:15px;}
        .stat-label {color:#666; font-size:0.9em; margin-bottom:5px;}
        .stat-value {font-size:2em; font-weight:bold; color:#333;}
        .quick-actions {background:white; padding:30px; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.1); margin-bottom:30px;}
        .section-title {font-size:1.5em; margin-bottom:20px; color:#333;}
        .action-buttons {display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:15px;}
        .action-btn {padding:15px 25px; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:white; text-decoration:none; border-radius:8px; font-weight:600; text-align:center; transition:transform 0.2s;}
        .action-btn:hover {transform:translateY(-3px); box-shadow:0 5px 15px rgba(102,126,234,0.3);}
        .top-rentals {background:white; padding:30px; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.1);}
        .rental-item {display:flex; justify-content:space-between; align-items:center; padding:15px; background:#f8f9fa; border-radius:8px; margin-bottom:10px;}
        .rental-info {flex:1;}
        .rental-name {font-weight:600; color:#333; margin-bottom:5px;}
        .rental-location {color:#666; font-size:0.9em;}
        .rental-revenue {font-weight:bold; color:#667eea; font-size:1.2em;}
        @media (max-width:768px) {.welcome h1{font-size:1.8em;} .stats-grid{grid-template-columns:1fr;}}
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="<?= BASE_PATH ?>" class="logo">🏠 RentHub</a>
        <div class="nav-links">
            <a href="<?= BASE_PATH ?>">Accueil</a>
            <?php if ($currentUser): ?>
                <a href="<?= BASE_PATH ?>/dashboard">Dashboard</a>
                <?php if ($currentUser->isHost()): ?>
                    <a href="<?= BASE_PATH ?>/my-rentals">Mes logements</a>
                <?php endif; ?>
                <a href="<?= BASE_PATH ?>/my-bookings">Mes réservations</a>
                <a href="<?= BASE_PATH ?>/logout">Déconnexion</a>
            <?php else: ?>
                <a href="<?= BASE_PATH ?>/login">Connexion</a>
                <a href="<?= BASE_PATH ?>/register">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome">
        <h1>👋 Bienvenue, <?= htmlspecialchars($currentUser->getFullName()) ?></h1>
        <p>
            <?php if ($currentUser->isAdmin()): ?>
                Vous êtes connecté en tant qu'administrateur
            <?php elseif ($currentUser->isHost()): ?>
                Gérez vos logements et suivez vos réservations
            <?php else: ?>
                Découvrez et réservez des logements incroyables
            <?php endif; ?>
        </p>
    </div>

    <!-- Stats -->
    <?php if ($currentUser->isAdmin()): ?>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon">👥</div><div class="stat-label">Utilisateurs</div><div class="stat-value"><?= number_format($stats['total_users']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">🏠</div><div class="stat-label">Logements</div><div class="stat-value"><?= number_format($stats['total_rentals']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-label">Réservations</div><div class="stat-value"><?= number_format($stats['total_bookings']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-label">Revenus totaux</div><div class="stat-value"><?= number_format($stats['total_revenue'],2) ?> MAD</div></div>
        </div>

        <?php if (!empty($stats['top_rentals'])): ?>
        <div class="top-rentals">
            <h2 class="section-title">🏆 Top 10 Logements</h2>
            <?php foreach($stats['top_rentals'] as $rental): ?>
                <div class="rental-item">
                    <div class="rental-info">
                        <div class="rental-name"><?= htmlspecialchars($rental['title']) ?></div>
                        <div class="rental-location">📍 <?= htmlspecialchars($rental['city']) ?> • <?= $rental['booking_count'] ?> réservations</div>
                    </div>
                    <div class="rental-revenue"><?= number_format($rental['total_revenue'],2) ?> MAD</div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    <?php elseif ($currentUser->isHost()): ?>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon">🏠</div><div class="stat-label">Mes logements</div><div class="stat-value"><?= number_format($hostStats['total_rentals']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-label">Réservations reçues</div><div class="stat-value"><?= number_format($hostStats['total_bookings']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-label">Revenus générés</div><div class="stat-value"><?= number_format($hostStats['total_revenue'],2) ?> MAD</div></div>
            <div class="stat-card"><div class="stat-icon">📊</div><div class="stat-label">Revenu moyen/logement</div><div class="stat-value"><?= number_format($hostStats['average_revenue_per_rental'],2) ?> MAD</div></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-label">Mes réservations confirmées</div><div class="stat-value"><?= number_format($userStats['confirmed_bookings']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">💳</div><div class="stat-label">Total dépensé</div><div class="stat-value"><?= number_format($userStats['total_spent'],2) ?> MAD</div></div>
        </div>

    <?php else: ?>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-label">Total réservations</div><div class="stat-value"><?= number_format($userStats['total_bookings']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-label">Réservations confirmées</div><div class="stat-value"><?= number_format($userStats['confirmed_bookings']) ?></div></div>
            <div class="stat-card"><div class="stat-icon">💳</div><div class="stat-label">Total dépensé</div><div class="stat-value"><?= number_format($userStats['total_spent'],2) ?> MAD</div></div>
            <div class="stat-card"><div class="stat-icon">❤️</div><div class="stat-label">Favoris</div><div class="stat-value"><?= number_format($favoriteCount) ?></div></div>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="section-title">Actions rapides</h2>
        <div class="action-buttons">
            <a href="<?= BASE_PATH ?>/rentals" class="action-btn">🔍 Chercher un logement</a>
            <a href="<?= BASE_PATH ?>/my-bookings" class="action-btn">📅 Mes réservations</a>
            <?php if ($currentUser->isHost()): ?>
                <a href="<?= BASE_PATH ?>/my-rentals" class="action-btn">🏠 Mes logements</a>
                <a href="<?= BASE_PATH ?>/rentals/create" class="action-btn">➕ Ajouter un logement</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
