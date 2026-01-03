<?php
require_once __DIR__ . '/../../config/app.php';
use Services\AuthService;
use Repositories\RentalRepository;

$authService = new AuthService();
$currentUser = $authService->getCurrentUser();
$rentalRepo = new RentalRepository();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réservations - RentHub</title>
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
        .page-title {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 30px;
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
        .booking-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }
        .booking-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .booking-id {
            color: #666;
            font-size: 0.9em;
        }
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }
        .booking-price {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .price-label {
            color: #666;
            margin-bottom: 5px;
        }
        .price-value {
            font-size: 2em;
            color: #667eea;
            font-weight: bold;
        }
        .booking-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            border: none;
            cursor: pointer;
        }
        .btn-view {
            background: #667eea;
            color: white;
        }
        .btn-receipt {
            background: #28a745;
            color: white;
        }
        .btn-cancel {
            background: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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
            .booking-header {
                flex-direction: column;
                gap: 15px;
            }
            .booking-actions {
                flex-direction: column;
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

    <div class="container">
        <h1 class="page-title">Mes réservations</h1>
        
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

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <h2>📅 Aucune réservation</h2>
                <p>Vous n'avez pas encore de réservation</p>
                <a href="<?= BASE_PATH ?>/rentals" class="btn btn-view" style="display: inline-block; margin-top: 20px;">Explorer les logements</a>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): 
                $rental = $rentalRepo->findById($booking->getRentalId());
            ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <div class="booking-title"><?= htmlspecialchars($rental->getTitle()) ?></div>
                            <div class="booking-id">Réservation #<?= str_pad($booking->getId(), 6, '0', STR_PAD_LEFT) ?></div>
                        </div>
                        <span class="status-badge status-<?= $booking->getStatus() ?>">
                            <?= $booking->getStatus() === 'confirmed' ? '✓ Confirmée' : '✗ Annulée' ?>
                        </span>
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail-item">
                            <span class="detail-label">📍 Ville</span>
                            <span class="detail-value"><?= htmlspecialchars($rental->getCity()) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">📅 Check-in</span>
                            <span class="detail-value"><?= $booking->getCheckIn()->format('d/m/Y') ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">📅 Check-out</span>
                            <span class="detail-value"><?= $booking->getCheckOut()->format('d/m/Y') ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">🌙 Nuits</span>
                            <span class="detail-value"><?= $booking->getNights() ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">👥 Voyageurs</span>
                            <span class="detail-value"><?= $booking->getNumberOfGuests() ?></span>
                        </div>
                    </div>
                    
                    <div class="booking-price">
                        <div class="price-label">Montant total</div>
                        <div class="price-value"><?= number_format($booking->getTotalPrice(), 2) ?> MAD</div>
                    </div>
                    
                    <div class="booking-actions">
                        <a href="<?= BASE_PATH ?>/rentals/<?= $rental->getId() ?>" class="btn btn-view">Voir le logement</a>
                        
                        <?php if ($booking->getStatus() === 'confirmed'): ?>
                            <a href="<?= BASE_PATH ?>/bookings/<?= $booking->getId() ?>/receipt" class="btn btn-receipt">📄 Télécharger le reçu</a>
                            <form method="POST" action="<?= BASE_PATH ?>/bookings/<?= $booking->getId() ?>/cancel" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                <button type="submit" class="btn btn-cancel">Annuler la réservation</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>