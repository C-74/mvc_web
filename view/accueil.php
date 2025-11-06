<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
        .btn { display: inline-block; padding: 15px 30px; margin: 20px; font-size: 18px; text-decoration: none; background-color: #007BFF; color: white; border-radius: 8px; }
        .btnRed { display: inline-block; padding: 15px 30px; margin: 20px; font-size: 18px; text-decoration: none; background-color: #ff2a00ff; color: white; border-radius: 8px; }
        .btn:hover { background-color: #0056b3; }
        .btnRed:hover { background-color: #cc2200ff; }
    </style>
</head>
<body>
    <h1>Bienvenue sur le gestionnaire du site</h1>
    <a href="index.php?action=utilisateurs" class="btn">Gestion des Utilisateurs</a>
    <a href="index.php?action=produits" class="btn">Gestion des Produits</a>
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
