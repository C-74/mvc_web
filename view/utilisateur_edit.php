<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="password"], input[type="email"] {
            width: 300px; padding: 5px;
        }
        input[type="submit"] {
            margin-top: 15px; padding: 10px 20px;
            background-color: #007bff; color: white;
            border: none; border-radius: 5px; cursor: pointer;
        }
        input[type="submit"]:hover { opacity: 0.9; }
        a { display: inline-block; margin-top: 15px; }
        .erreur { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Modifier un utilisateur</h2>

    <!-- Affichage des erreurs -->
    <?php if (!empty($erreur)) : ?>
        <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label>Login :</label>
        <input type="text" name="login" required value="<?= htmlspecialchars($utilisateur['uti_login'] ?? '') ?>">

        <label>Mot de passe (laisser vide pour ne pas modifier) :</label>
        <input type="password" name="mdp" value="">

        <label>Confirmer le mot de passe :</label>
        <input type="password" name="mdp_conf" value="">

        <label>Mail :</label>
        <input type="email" name="mail" required value="<?= htmlspecialchars($utilisateur['uti_mail'] ?? '') ?>">

        <br>

        <input type="submit" value="Modifier">
    </form>

    <a href="index.php?action=utilisateurs">Retour à la liste</a>
</body>
</html>
