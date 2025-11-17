<?php
$type = file_get_contents("typeProduits.json");
$listType = json_decode($type, true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un produit</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="date"] { width: 300px; padding: 5px; }
        input[type="submit"] { margin-top: 15px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        input[type="submit"]:hover { opacity: 0.9; }
        a { display: inline-block; margin-top: 15px; }
        .erreur { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Modifier un produit</h2>

    <!-- Affichage des erreurs -->
    <?php if (!empty($erreur)) : ?>
        <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>

    <label>Désignation :</label>
    <input type="text" name="designation" required value="<?= htmlspecialchars($produit['pro_designation']) ?>">

    <label>Type :</label>
    <select name="type" required>
        <?php
        foreach ($listType as $type) {
            $selected = ($type['categorie'] === $produit['pro_type']) ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($type['categorie']) . '" ' . $selected . '>' . htmlspecialchars($type['categorie']) . '</option>';
        }
        ?>
    </select>

        <label>Prix HT :</label>
        <input type="number" step="0.01" min="0.01" name="prix" id="prix" required 
            value="<?= $produit['pro_prix_ht'] ?>" 
            onblur="arrondirPrix(this)">

        <label>Promotion (%) :</label>
        <input type="number" step="0.01" min="0" max="100" name="promo"
            value="<?= htmlspecialchars($produit['pro_promo'] ?? '') ?>">

        <script>
            function arrondirPrix(input) {
                let valeur = parseFloat(input.value.replace(',', '.')); // convertir virgule en point si nécessaire
                if (!isNaN(valeur)) {
                    input.value = valeur.toFixed(2); // arrondi à 2 décimales
                }
            }
        </script>

        <label>Date d'arrivée :</label>
        <input type="date" name="date_arrive" required value="<?= $produit['pro_date_arrive'] ?>"
            min="<?= date('Y-m-d') ?>">

        <label>Stock :</label>
        <input type="number" name="stock" min="1" required value="<?= $produit['pro_stock'] ?>">

        <?php if (!empty($produit['pro_image'])) : ?>
            <p>Image actuelle : <?= htmlspecialchars($produit['pro_image']) ?></p>
        <?php endif; ?>

        <label>Nouvelle image (optionnelle) :</label>
        <input type="file" name="image" accept="image/*">

        <br>
        
        <input type="submit" value="Modifier">
    </form>
    <a href="index.php?action=produits">Retour à la liste</a>
</body>
</html>
