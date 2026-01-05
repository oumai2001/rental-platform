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
    <title>Logements disponibles - RentHub</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
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
        .nav-links a:hover {
            color: #667eea;
        }
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .search-container {
            max-width: 900px;
            margin: 30px auto 0;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            color: #555;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input, .form-group select {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
        }
        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            grid-column: 1 / -1;
        }
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
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
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .rental-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .rental-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
            display: flex;
            align-items: center;
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
        }
        .rating {
            color: #ffa500;
            margin-left: 10px;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-results h2 {
            font-size: 2em;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.8em;
            }
            .search-form {
                grid-template-columns: 1fr;
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


    <div class="hero">
        <h1>Trouvez votre logement idéal</h1>
        <p>Des milliers de logements disponibles dans tout le Maroc</p>
        
        <div class="search-container">
            <form method="GET" action="/rentals" class="search-form">
                <div class="form-group">
                    <label>Ville</label>
                    <input type="text" name="city" placeholder="Ex: Casablanca" value="<?= htmlspecialchars($_GET['city'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Prix min</label>
                    <input type="number" name="min_price" placeholder="0 MAD" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Prix max</label>
                    <input type="number" name="max_price" placeholder="5000 MAD" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Voyageurs</label>
                    <input type="number" name="min_guests" placeholder="1" value="<?= htmlspecialchars($_GET['min_guests'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Check-in</label>
                    <input type="date" name="check_in" value="<?= htmlspecialchars($_GET['check_in'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Check-out</label>
                    <input type="date" name="check_out" value="<?= htmlspecialchars($_GET['check_out'] ?? '') ?>">
                </div>
                
                <button type="submit" class="btn-search">🔍 Rechercher</button>
            </form>
        </div>
    </div>

    <div class="container">
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
            <div class="no-results">
                <h2>😔 Aucun logement trouvé</h2>
                <p>Essayez de modifier vos critères de recherche</p>
            </div>
        <?php else: ?>
            <div class="rentals-grid">
                <?php foreach ($rentals as $rental): ?>
                    <a href="/rentals/<?= $rental->getId() ?>" class="rental-card">
                        <div class="rental-image">🏡</div>
                        <div class="rental-info">
                            <div class="rental-title"><?= htmlspecialchars($rental->getTitle()) ?></div>
                            <div class="rental-city">
                                📍 <?= htmlspecialchars($rental->getCity()) ?>
                                <?php if ($rental->getAverageRating() > 0): ?>
                                    <span class="rating">⭐ <?= number_format($rental->getAverageRating(), 1) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="rental-details">
                                <span>👥 <?= $rental->getMaxGuests() ?> voyageurs</span>
                                <span>🛏️ <?= $rental->getBedrooms() ?> chambres</span>
                                <span>🚿 <?= $rental->getBathrooms() ?> SDB</span>
                            </div>
                            <div class="rental-price"><?= number_format($rental->getPricePerNight(), 2) ?> MAD / nuit</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>