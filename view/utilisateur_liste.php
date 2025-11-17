<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        a.btn { display: inline-block; padding: 8px 15px; margin: 5px; text-decoration: none; color: white; border-radius: 5px; }
        .accueil { background-color: #6c757d; /* gris */ color: white; }
        .create { background-color: #28a745; }
        .edit { background-color: #007bff; }
        .delete { background-color: #dc3545; }
        a.btn:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <h1>Liste des utilisateurs</h1>
    <!-- Si l'utilisateur est admin, afficher le bouton "Ajouter un utilisateur" -->
    <?php if ($_SESSION['user']->uti_admin): ?>
        <a href="index.php?action=utilisateurs&subaction=create" class="btn create">Ajouter un utilisateur</a>
    <?php endif; ?>
    <a href="index.php" class="btn accueil">Accueil</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Mail</th>
            <th>ID Compte</th>
            <th>Date Création</th>
            <th>Date Connexion</th>
            <?php if ($_SESSION['user']->uti_admin): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
        <?php while ($row = $utilisateurs->fetch(PDO::FETCH_ASSOC)) : ?>
        <tr>
            <td><?= $row['uti_idutilisateur'] ?></td>
            <td><?= htmlspecialchars($row['uti_login']) ?></td>
            <td><?= htmlspecialchars($row['uti_mail']) ?></td>
            <td><?= $row['uti_idcompte'] ?></td>
            <td><?= $row['uti_date_creation'] ?></td>
            <td><?= $row['uti_date_connexion'] ?></td>
            <?php if ($_SESSION['user']->uti_admin): ?>
            <td>
                <a href="index.php?action=utilisateurs&subaction=edit&id=<?= $row['uti_idutilisateur'] ?>" class="btn edit">✏️</a>
                <a href="index.php?action=utilisateurs&subaction=delete&id=<?= $row['uti_idutilisateur'] ?>" class="btn delete" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
