<?php
// Si le formulaire a déjà été soumis, on récupère les valeurs pour pré-remplir les champs
$type_val = $_POST['type'] ?? '';
$designation_val = $_POST['designation'] ?? '';
$prix_val = $_POST['prix'] ?? '';
$date_arrive_val = $_POST['date_arrive'] ?? '';
$stock_val = $_POST['stock'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="date"], input[type="decimal"] { width: 300px; padding: 5px; }
        input[type="submit"] { margin-top: 15px; padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        input[type="submit"]:hover { opacity: 0.9; }
        a { display: inline-block; margin-top: 15px; }
        .erreur { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Ajouter un produit</h2>

    <!-- Affichage des erreurs -->
    <?php if (!empty($erreur)) : ?>
        <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label>Type :</label>
        <input type="text" name="type" required value="<?= htmlspecialchars($type_val) ?>">

        <label>Désignation :</label>
        <input type="text" name="designation" required value="<?= htmlspecialchars($designation_val) ?>">

        <label>Prix HT :</label>
        <input type="number" step="0.01" min="0.01" name="prix" id="prix" required 
            value="<?= htmlspecialchars($prix_val) ?>" 
            onblur="arrondirPrix(this)">

        <script>
            function arrondirPrix(input) {
                let valeur = parseFloat(input.value.replace(',', '.')); // convertir virgule en point si nécessaire
                if (!isNaN(valeur)) {
                    input.value = valeur.toFixed(2); // arrondi à 2 décimales
                }
            }
        </script>

        <label>Date d'arrivée :</label>
        <input type="date" name="date_arrive" required value="<?= htmlspecialchars($date_arrive_val) ?>"
            min="<?= date('Y-m-d') ?>">

        <label>Stock :</label>
        <input type="number" name="stock" min="1" required value="<?= htmlspecialchars($stock_val) ?>">

        <br>

        <input type="submit" value="Enregistrer">
    </form>
    <a href="index.php?action=produits">Retour à la liste</a>
</body>
</html>
