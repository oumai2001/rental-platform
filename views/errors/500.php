<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erreur serveur</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
        }
        h1 {
            font-size: 8em;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2em;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .error-details {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: white;
            color: #dc3545;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1em;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <h2>Erreur serveur</h2>
        <p>Une erreur s'est produite lors du traitement de votre requête.</p>
        
        <?php if (isset($e)): ?>
        <div class="error-details">
            <strong>Message:</strong> <?= htmlspecialchars($e->getMessage()) ?><br>
            <strong>Fichier:</strong> <?= htmlspecialchars($e->getFile()) ?><br>
            <strong>Ligne:</strong> <?= $e->getLine() ?>
        </div>
        <?php endif; ?>
        
        <a href="/" class="btn">← Retour à l'accueil</a>
    </div>
</body>
</html>