<?php
use Services\AuthService;

require_once __DIR__ . '/../../config/app.php';

$authService = new AuthService();
$currentUser = $authService->getCurrentUser();

if ($currentUser) {
    header('Location: ' . BASE_PATH . '/dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - RentHub</title>

    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Segoe UI',Tahoma,Verdana,sans-serif;
            background:linear-gradient(135deg,#667eea,#764ba2);
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .container{
            background:#fff;
            border-radius:20px;
            box-shadow:0 20px 60px rgba(0,0,0,.3);
            max-width:900px;
            width:100%;
            display:flex;
            overflow:hidden;
        }
        .left{
            flex:1;
            padding:60px 40px;
            color:#fff;
            background:linear-gradient(135deg,#667eea,#764ba2);
        }
        .right{
            flex:1;
            padding:60px 40px;
        }
        h1{font-size:2.5em;margin-bottom:20px}
        h2{margin-bottom:30px;color:#333}
        .form-group{margin-bottom:20px}
        label{display:block;margin-bottom:8px;font-weight:600}
        input{
            width:100%;
            padding:12px;
            border-radius:8px;
            border:2px solid #e0e0e0;
        }
        button{
            width:100%;
            padding:14px;
            background:#667eea;
            color:#fff;
            border:none;
            border-radius:8px;
            font-size:1.1em;
            cursor:pointer;
        }
        .alert{padding:12px;border-radius:6px;margin-bottom:15px}
        .success{background:#d4edda;color:#155724}
        .error{background:#f8d7da;color:#721c24}
        .register{text-align:center;margin-top:20px}
        .register a{color:#667eea;text-decoration:none;font-weight:600}
    </style>
</head>
<body>

<div class="container">
    <div class="left">
        <h1>🏠 RentHub</h1>
        <p>Connectez-vous pour gérer vos réservations et logements.</p>
    </div>

    <div class="right">
        <h2>Connexion</h2>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_PATH ?>/login">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Se connecter</button>

            <div class="register">
                Pas encore de compte ?
                <a href="<?= BASE_PATH ?>/register">S'inscrire</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
