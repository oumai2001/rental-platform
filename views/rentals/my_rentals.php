<?php
require_once __DIR__ . '/../../config/app.php';
use Services\AuthService;
$authService = new AuthService();
$currentUser = $authService->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes logements - RentHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
        }
        .nav-links a {
            margin-left: 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-title {
            font-size: 2.5em;
            color: #333;
        }
        .btn-add {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        .rentals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        .rental-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .rental-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4em;
        }
        .rental-info {
            padding: 20px;
        }
        .rental-title {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .rental-city {
            color: #666;
            margin-bottom: 15px;
        }
        .rental-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #777;
        }
        .rental-price {
            font-size: 1.2em;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .btn-view {
            background: #667eea;
            color: white;
        }
        .btn-edit {
            background: #ffa500;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }
        .empty-state h2 {
            font-size: 2em;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
   <nav class="navbar">
    <div class="nav-container">
        <a href="<?= BASE_PATH ?>/" class="logo">🏠 RentHub</a>
        <div class="nav-links">
            <a href="<?= BASE_PATH ?>/">Accueil</a>
            <a href="<?= BASE_PATH ?>/dashboard">Dashboard</a>
            <a href="<?= BASE_PATH ?>/my-rentals">Mes logements</a>
            <a href="<?= BASE_PATH ?>/my-bookings">Mes réservations</a>
            <a href="<?= BASE_PATH ?>/logout">Déconnexion</a>
        </div>
    </div>
</nav>

    <div class="container">
        <div class="header">
            <h1 class="page-title">Mes logements</h1>
            <a href="<?= BASE_PATH ?>/rentals/create" class="btn-add">+ Ajouter un logement</a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($rentals)): ?>
            <div class="empty-state">
                <h2>🏡 Aucun logement</h2>
                <p>Vous n'avez pas encore ajouté de logement</p>
                <a href="<?= BASE_PATH ?>/rentals/create" class="btn-add" style="display: inline-block; margin-top: 20px;">Ajouter votre premier logement</a>
            </div>
        <?php else: ?>
            <div class="rentals-grid">
                <?php foreach ($rentals as $rental): ?>
                    <div class="rental-card">
                        <div class="rental-image">🏡</div>
                        <div class="rental-info">
                            <span class="status-badge <?= $rental->isActive() ? 'status-active' : 'status-inactive' ?>">
                                <?= $rental->isActive() ? '✓ Actif' : '✗ Inactif' ?>
                            </span>
                            
                            <div class="rental-title"><?= htmlspecialchars($rental->getTitle()) ?></div>
                            <div class="rental-city">📍 <?= htmlspecialchars($rental->getCity()) ?></div>
                            
                            <div class="rental-details">
                                <span>👥 <?= $rental->getMaxGuests() ?></span>
                                <span>🛏️ <?= $rental->getBedrooms() ?></span>
                                <span>🚿 <?= $rental->getBathrooms() ?></span>
                            </div>
                            
                            <div class="rental-price"><?= number_format($rental->getPricePerNight(), 2) ?> MAD / nuit</div>
                            
                            <div class="actions">
                                <a href="<?= BASE_PATH ?>/rentals/<?= $rental->getId() ?>" class="btn btn-view">Voir</a>
                                <a href="<?= BASE_PATH ?>/rentals/<?= $rental->getId() ?>/edit" class="btn btn-edit">Modifier</a>
                                <form method="POST" action="<?= BASE_PATH ?>/rentals/<?= $rental->getId() ?>/delete" style="flex: 1;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce logement ?');">
                                    <button type="submit" class="btn btn-delete" style="width: 100%;">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>