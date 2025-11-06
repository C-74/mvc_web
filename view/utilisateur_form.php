<?php
// Si le formulaire a déjà été soumis, on récupère les valeurs pour pré-remplir les champs
$login_val = $_POST['login'] ?? '';
$mdp_val = '';
$mdp_conf_val = '';
$mail_val = $_POST['mail'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="password"], input[type="email"] { width: 300px; padding: 5px; }
        input[type="submit"] { margin-top: 15px; padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        input[type="submit"]:hover { opacity: 0.9; }
        a { display: inline-block; margin-top: 15px; }
        .erreur { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Ajouter un utilisateur</h2>

    <!-- Affichage des erreurs -->
    <?php if (!empty($erreur)) : ?>
        <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label>Login :</label>
        <input type="text" name="login" required value="<?= htmlspecialchars($login_val) ?>">

        <label>Mot de passe :</label>
        <input type="password" name="mdp" required value="<?= htmlspecialchars($mdp_val) ?>">

        <label>Confirmer le mot de passe :</label>
        <input type="password" name="mdp_conf" required value="<?= htmlspecialchars($mdp_conf_val) ?>">

        <label>Mail :</label>
        <input type="text" name="mail" required value="<?= htmlspecialchars($mail_val) ?>">

        <br>

        <input type="submit" value="Enregistrer">
    </form>

    <a href="index.php?action=utilisateurs">Retour à la liste</a>
</body>
</html>
