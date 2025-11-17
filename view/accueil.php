<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
        .btn { display: inline-block; padding: 15px 30px; margin: 20px; font-size: 18px; text-decoration: none; background-color: #007BFF; color: white; border-radius: 8px; }
        .btnRed { display: inline-block; padding: 15px 30px; margin: 20px; font-size: 18px; text-decoration: none; background-color: #ff2a00ff; color: white; border-radius: 8px; }
        .btnYellow { display: inline-block; padding: 15px 30px; margin: 20px; font-size: 18px; text-decoration: none; background-color: #ffc107; color: #333; border-radius: 8px; }
        .btn:hover { background-color: #0056b3; }
        .btnRed:hover { background-color: #cc2200ff; }
        .btnYellow:hover { background-color: #e0a800; }
        .cart-badge { background: #dc3545; color: white; padding: 2px 10px; border-radius: 12px; font-size: 0.8em; vertical-align: super; }
    </style>
</head>
<body>
    <h1>Bienvenue sur le gestionnaire du site</h1>
    <a href="index.php?action=utilisateurs" class="btn">Gestion des Utilisateurs</a>
    <a href="index.php?action=produits" class="btn">Gestion des Produits</a>
    <?php
    require_once __DIR__ . '/../model/Panier.php';
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $panierTemp = new Panier($database->getConnection());
    $nbArticles = $panierTemp->getNombreArticles();
    ?>
    <a href="index.php?action=panier" class="btnYellow">
        🛒 Mon Panier
        <?php if ($nbArticles > 0): ?>
            <span class="cart-badge"><?= $nbArticles ?></span>
        <?php endif; ?>
    </a>
    <br>
    <a href="#" onclick="return confirmLogout()" class="btnRed">Déconnexion</a>
    <script>
        function confirmLogout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = 'index.php?action=logout';
                return true;
            }
            return false;
        }
    </script>
</body>
</html>
