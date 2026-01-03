<?php
use Services\AuthService;
$authService = new AuthService();
$currentUser = $authService->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un logement - RentHub</title>
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
            max-width: 800px;
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
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 2em;
            margin-bottom: 30px;
            color: #333;
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
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn-submit {
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
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        @media (max-width: 768px) {
            .form-row {
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
                <a href="<?= BASE_PATH ?>/dashboard">Dashboard</a>
                <a href="<?= BASE_PATH ?>/my-rentals">Mes logements</a>
                <a href="<?= BASE_PATH ?>/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <a href="<?= BASE_PATH ?>/my-rentals" class="back-link">← Retour à mes logements</a>
        
        <div class="form-container">
            <h1 class="form-title">Ajouter un nouveau logement</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_PATH ?>/rentals/create">
                <div class="form-group">
                    <label for="title">Titre du logement *</label>
                    <input type="text" id="title" name="title" required placeholder="Ex: Appartement moderne avec vue sur mer">
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required placeholder="Décrivez votre logement en détail..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">Ville *</label>
                        <input type="text" id="city" name="city" required placeholder="Ex: Casablanca">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresse *</label>
                        <input type="text" id="address" name="address" required placeholder="Ex: Quartier Maarif">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price_per_night">Prix par nuit (MAD) *</label>
                        <input type="number" id="price_per_night" name="price_per_night" required min="0" step="0.01" placeholder="500">
                    </div>
                    
                    <div class="form-group">
                        <label for="max_guests">Nombre de voyageurs *</label>
                        <input type="number" id="max_guests" name="max_guests" required min="1" placeholder="4">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="bedrooms">Nombre de chambres *</label>
                        <input type="number" id="bedrooms" name="bedrooms" required min="0" placeholder="2">
                    </div>
                    
                    <div class="form-group">
                        <label for="bathrooms">Nombre de salles de bain *</label>
                        <input type="number" id="bathrooms" name="bathrooms" required min="1" placeholder="1">
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Ajouter le logement</button>
            </form>
        </div>
    </div>
</body>
</html>