<?php
require_once __DIR__ . '/../../config/app.php';
use Services\AuthService;
use Repositories\{ReviewRepository, UserRepository};

$authService = new AuthService();
$currentUser = $authService->getCurrentUser();
$reviewRepo = new ReviewRepository();
$userRepo = new UserRepository();
$reviews = $reviewRepo->findByRentalId($rental->getId());
$host = $userRepo->findById($rental->getHostId());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($rental->getTitle()) ?> - RentHub</title>
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
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .rental-header {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .rental-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8em;
            margin-bottom: 30px;
        }
        .rental-title {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #333;
        }
        .rental-location {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 20px;
        }
        .rental-stats {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }
        .stat {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1em;
            color: #555;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        .main-content, .booking-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }
        .description {
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }
        .host-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .host-avatar {
            width: 60px;
            height: 60px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            color: white;
        }
        .booking-card {
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        .price {
            font-size: 2em;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
        }
        .btn-book {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .review {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .review-author {
            font-weight: 600;
            color: #333;
        }
        .review-rating {
            color: #ffa500;
        }
        .review-text {
            color: #555;
            line-height: 1.6;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .booking-card {
                position: static;
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
                <a href="<?= BASE_PATH ?>/logout">Déconnexion</a>
            <?php else: ?>
                <a href="<?= BASE_PATH ?>/login">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>


    <div class="container">
        <a href="/rentals" class="back-link">← Retour aux logements</a>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="rental-header">
            <div class="rental-image">🏡</div>
            
            <h1 class="rental-title"><?= htmlspecialchars($rental->getTitle()) ?></h1>
            <div class="rental-location">📍 <?= htmlspecialchars($rental->getCity()) ?> - <?= htmlspecialchars($rental->getAddress()) ?></div>
            
            <div class="rental-stats">
                <div class="stat">👥 <?= $rental->getMaxGuests() ?> voyageurs</div>
                <div class="stat">🛏️ <?= $rental->getBedrooms() ?> chambres</div>
                <div class="stat">🚿 <?= $rental->getBathrooms() ?> salles de bain</div>
                <?php if ($rental->getAverageRating() > 0): ?>
                    <div class="stat">⭐ <?= number_format($rental->getAverageRating(), 1) ?> / 5</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-grid">
            <div class="main-content">
                <h2 class="section-title">Description</h2>
                <p class="description"><?= nl2br(htmlspecialchars($rental->getDescription())) ?></p>

                <div class="host-info">
                    <div class="host-avatar">👤</div>
                    <div>
                        <strong>Hôte: <?= htmlspecialchars($host->getFullName()) ?></strong><br>
                        <span style="color: #666;">Membre depuis <?= $host->getCreatedAt()->format('Y') ?></span>
                    </div>
                </div>

                <h2 class="section-title">Avis (<?= count($reviews) ?>)</h2>
                <?php if (empty($reviews)): ?>
                    <p style="color: #666;">Aucun avis pour le moment</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): 
                        $reviewer = $userRepo->findById($review->getUserId());
                    ?>
                        <div class="review">
                            <div class="review-header">
                                <span class="review-author"><?= htmlspecialchars($reviewer->getFullName()) ?></span>
                                <span class="review-rating">⭐ <?= $review->getRating() ?>/5</span>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($review->getComment()) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="booking-card">
                <div class="price"><?= number_format($rental->getPricePerNight(), 2) ?> MAD <span style="font-size: 0.5em; color: #666;">/ nuit</span></div>
                
                <?php if ($currentUser): ?>
                    <form method="POST" action="/bookings/create">
                        <input type="hidden" name="rental_id" value="<?= $rental->getId() ?>">
                        
                        <div class="form-group">
                            <label>Date d'arrivée</label>
                            <input type="date" name="check_in" required min="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Date de départ</label>
                            <input type="date" name="check_out" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Nombre de voyageurs</label>
                            <input type="number" name="number_of_guests" min="1" max="<?= $rental->getMaxGuests() ?>" required>
                        </div>
                        
                        <button type="submit" class="btn-book">Réserver maintenant</button>
                    </form>
                <?php else: ?>
                    <p style="text-align: center; color: #666; margin-bottom: 15px;">Connectez-vous pour réserver</p>
                    <a href="/login" style="display: block; text-align: center; padding: 15px; background: #667eea; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>