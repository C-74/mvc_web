<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des produits</title>
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
    <h1>Liste des produits</h1>
    <?php if ($_SESSION['user']->uti_admin):
    ?>
    <a href="index.php?action=produits&subaction=create" class="btn create">Ajouter un produit</a>
    <?php endif; ?>
    <a href="index.php" class="btn accueil">Accueil</a>
    <table>
        <tr>
            <th>Image</th>
            <th>ID</th>
            <th>Type</th>
            <th>Désignation</th>
            <th>Prix HT</th>
            <th>Date arrivée</th>
            <th>Timestamp arrivée</th>
            <th>Stock</th>
            <?php if ($_SESSION['user']->uti_admin):
            ?>
                <th>Actions</th>
            <?php endif; ?>

        </tr>
        <?php while ($row = $produits->fetch(PDO::FETCH_ASSOC)) : ?>
        <tr>
            <td>
                <?php
                // Si pro_image est renseigné, on l’utilise.
                // Sinon, on affiche une image commune.
                $imageSrc = !empty($row['pro_image'])
                    ? $row['pro_image']
                    : 'uploads/default.jpeg'; // à créer une fois pour toutes
                ?>
                <img src="<?= htmlspecialchars($imageSrc) ?>"
                     alt="Image produit"
                     style="max-width: 80px; max-height: 80px; object-fit: cover;">
            </td>
            
            <td><?= $row['pro_idproduit'] ?></td>
            <td><?= htmlspecialchars($row['pro_type']) ?></td>
            <td><?= htmlspecialchars($row['pro_designation']) ?></td>

            <td>
                <?php if (!empty($row['pro_promo']) && $row['pro_promo'] != 0) : ?>
                    <!-- Ancien prix barré en gris -->
                    <span style="text-decoration: line-through; color: grey;">
                        <?= number_format($row['pro_prix_ht'], 2, ',', ' ') ?> €
                    </span>
                    <br>
                    <!-- Nouveau prix remisé -->
                    <span style="color: red;">
                        <?php
                        $prixRemise = $row['pro_prix_ht'] * (1 - $row['pro_promo'] / 100);
                        echo number_format($prixRemise, 2, ',', ' ');
                        ?> €
                    </span>
                <?php else : ?>
                    <!-- Pas de promo : prix normal -->
                    <?= number_format($row['pro_prix_ht'], 2, ',', ' ') ?> €
                <?php endif; ?>
                
                <!-- Affichage de l'histo des prix -->
                <br>
                <?php $rowPrix = $historique->lireDepuisProduit($row['pro_idproduit']); ?>
                <?php if ($rowPrix && $rowPrix->rowCount() > 0) : ?>
                <details>
                    <summary style="cursor: pointer; background-color: #e9ecef; padding: 2px; border-radius: 3px;">
                        Historique
                    </summary>
                <?php while ($item = $rowPrix->fetch(PDO::FETCH_ASSOC)) : ?>   
                    <div style="margin-top: 5px; padding: 5px; background-color: #f9f9f9; ">
                        <small style="color: #333;">
                            <?=  number_format($item['hpr_prix_ht'], 2, ',', ' ') ?> €
                            <br>
                                le <?= date('d/m/Y à H:i', strtotime($item['hpr_date_modification'])) ?>
                        </small>
                    </div>
                <?php endwhile; ?>
                </details>
                <?php endif; ?>
                
                
            </td>


            <td><?= $row['pro_date_arrive'] ?></td>
            <td><?= $row['pro_timestamp_arrive'] ?></td>
            <td><?= $row['pro_stock'] ?></td>
            <?php if ($_SESSION['user']->uti_admin):
            ?>
            <td>
                <a href="index.php?action=produits&subaction=edit&id=<?= $row['pro_idproduit'] ?>" class="btn edit">✏️</a>
                <a href="index.php?action=produits&subaction=delete&id=<?= $row['pro_idproduit'] ?>" class="btn delete" onclick="return confirm('Supprimer ce produit ?')">🗑️</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>



