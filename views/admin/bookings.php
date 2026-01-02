<?php
require_once __DIR__ . '/../../config/app.php';
use Services\AuthService;
use Repositories\{RentalRepository, UserRepository};

$authService = new AuthService();
$currentUser = $authService->requireRole('admin');
$rentalRepo = new RentalRepository();
$userRepo = new UserRepository();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des réservations - Admin RentHub</title>
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
            max-width: 1400px;
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
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .admin-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .admin-header p {
            font-size: 1.1em;
            opacity: 0.9;
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
        .bookings-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            padding: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .table-title {
            font-size: 1.5em;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: #f8f9fa;
        }
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #e0e0e0;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-cancel {
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-cancel:hover {
            background: #c82333;
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
        @media (max-width: 1200px) {
            .bookings-table {
                overflow-x: auto;
            }
            table {
                min-width: 1000px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
    <div class="nav-container">
        <a href="<?= BASE_PATH ?>" class="logo">🏠 RentHub Admin</a>
        <div class="nav-links">
            <a href="<?= BASE_PATH ?>/dashboard">Dashboard</a>
            <a href="<?= BASE_PATH ?>/rentals">Logements</a>
            <a href="<?= BASE_PATH ?>/logout">Déconnexion</a>
        </div>
    </div>
</nav>

    <div class="container">
        <div class="admin-header">
            <h1>⚙️ Gestion des réservations</h1>
            <p>Administration complète de toutes les réservations de la plateforme</p>
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

        <div class="bookings-table">
            <div class="table-header">
                <div class="table-title">📅 Toutes les réservations (<?= count($bookings) ?>)</div>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <h2>📋 Aucune réservation</h2>
                    <p>Il n'y a pas encore de réservations sur la plateforme</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Voyageur</th>
                            <th>Logement</th>
                            <th>Ville</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Voyageurs</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): 
                            $rental = $rentalRepo->findById($booking->getRentalId());
                            $user = $userRepo->findById($booking->getUserId());
                        ?>
                            <tr>
                                <td>#<?= str_pad($booking->getId(), 6, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($user->getFullName()) ?></strong><br>
                                    <small style="color: #666;"><?= htmlspecialchars($user->getEmail()) ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($rental->getTitle()) ?></strong>
                                </td>
                                <td>📍 <?= htmlspecialchars($rental->getCity()) ?></td>
                                <td><?= $booking->getCheckIn()->format('d/m/Y') ?></td>
                                <td><?= $booking->getCheckOut()->format('d/m/Y') ?></td>
                                <td>👥 <?= $booking->getNumberOfGuests() ?></td>
                                <td><strong><?= number_format($booking->getTotalPrice(), 2) ?> MAD</strong></td>
                                <td>
                                    <span class="status-badge status-<?= $booking->getStatus() ?>">
                                        <?= $booking->getStatus() === 'confirmed' ? '✓ Confirmée' : '✗ Annulée' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking->getStatus() === 'confirmed'): ?>
                                        <form method="POST" action="/bookings/<?= $booking->getId() ?>/cancel" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                            <button type="submit" class="btn-cancel">Annuler</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>