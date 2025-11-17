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
        th.sortable { cursor: pointer; }
        .sort-arrow { margin-left: 5px; font-size: 0.8em; }
        a.btn { display: inline-block; padding: 8px 15px; margin: 5px; text-decoration: none; color: white; border-radius: 5px; }
        .accueil { background-color: #6c757d; /* gris */ color: white; }
        .create { background-color: #28a745; }
        .edit { background-color: #007bff; }
        .delete { background-color: #dc3545; }
        .panier { background-color: #ffc107; color: #333; }
        .add-cart { background-color: #17a2b8; }
        a.btn:hover { opacity: 0.8; }
        button.btn:hover { opacity: 0.8; }
        .cart-badge { background: #dc3545; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; }
    </style>
</head>
<body>
    <h1>Liste des produits</h1>
    <?php if ($_SESSION['user']->uti_admin):
    ?>
    <a href="index.php?action=produits&subaction=create" class="btn create">Ajouter un produit</a>
    <?php endif; ?>
    <a href="index.php" class="btn accueil">Accueil</a>
    <?php
    require_once __DIR__ . '/../model/Panier.php';
    require_once __DIR__ . '/../config/database.php';
    $databasePanier = new Database();
    $panierTemp = new Panier($databasePanier->getConnection());
    $nbArticles = $panierTemp->getNombreArticles();
    ?>
    <a href="index.php?action=panier" class="btn panier">
        🛒 Mon Panier
        <?php if ($nbArticles > 0): ?>
            <span class="cart-badge"><?= $nbArticles ?></span>
        <?php endif; ?>
    </a>

    <!-- Filtre par type -->
    <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <form method="GET" action="index.php" style="display: inline-flex; align-items: center; gap: 10px;">
            <input type="hidden" name="action" value="produits">
            <label for="type" style="font-weight: bold;">Filtrer par type :</label>
            <select name="type" id="type" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="">-- Tous les types --</option>
                <?php
                $typesArray = $typesDisponibles->fetchAll(PDO::FETCH_ASSOC);
                foreach ($typesArray as $typeOption):
                ?>
                    <option value="<?= htmlspecialchars($typeOption['pro_type']) ?>"
                            <?= ($typeFiltre === $typeOption['pro_type']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($typeOption['pro_type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Filtrer
            </button>
            <?php if ($typeFiltre !== ''): ?>
                <a href="index.php?action=produits" style="padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
                    Réinitialiser
                </a>
            <?php endif; ?>
        </form>
        <?php if ($typeFiltre !== ''): ?>
            <p style="margin-top: 10px; color: #495057;">
                <strong>Filtre actif :</strong> <?= htmlspecialchars($typeFiltre) ?>
            </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th class="sortable" data-type="number">ID <span class="sort-arrow">▲</span></th>
                <th class="sortable" data-type="string">Type <span class="sort-arrow">▲</span></th>
                <th class="sortable" data-type="string">Désignation <span class="sort-arrow">▲</span></th>
                <th class="sortable" data-type="number">Prix HT <span class="sort-arrow">▲</span></th>
                <th class="sortable" data-type="date">Date arrivée <span class="sort-arrow">▲</span></th>
                <th class="sortable" data-type="number">Timestamp arrivée <span class="sort-arrow">▲</span></th>
                <th class="sortable" data-type="number">Stock <span class="sort-arrow">▲</span></th>
                <th>Panier</th>
                <?php if ($_SESSION['user']->uti_admin): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
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
            <td>
                <?php if ($row['pro_stock'] > 0): ?>
                    <form method="POST" action="index.php?action=panier&subaction=ajouter" style="display: inline;">
                        <input type="hidden" name="id_produit" value="<?= $row['pro_idproduit'] ?>">
                        <input type="hidden" name="redirect" value="index.php?action=produits<?= $typeFiltre ? '&type=' . urlencode($typeFiltre) : '' ?>">
                        <input type="number" name="quantite" value="1" min="1" max="<?= $row['pro_stock'] ?>" style="width: 60px; padding: 5px;">
                        <button type="submit" class="btn add-cart" style="padding: 5px 10px; border: none; cursor: pointer;">🛒+</button>
                    </form>
                <?php else: ?>
                    <span style="color: #dc3545; font-weight: bold;">Rupture</span>
                <?php endif; ?>
            </td>
            <?php if ($_SESSION['user']->uti_admin):
            ?>
            <td>
                <a href="index.php?action=produits&subaction=edit&id=<?= $row['pro_idproduit'] ?>" class="btn edit">✏️</a>
                <a href="index.php?action=produits&subaction=delete&id=<?= $row['pro_idproduit'] ?>" class="btn delete" onclick="return confirm('Supprimer ce produit ?')">🗑️</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.querySelector('table');
            if (!table) return;

            const tbody = table.querySelector('tbody');
            const headers = table.querySelectorAll('th.sortable');

            headers.forEach(function (header) {
                header.dataset.order = 'asc'; // ordre initial

                header.addEventListener('click', function () {
                    const currentOrder = header.dataset.order === 'asc' ? 'desc' : 'asc';
                    const type = header.dataset.type || 'string';
                    const columnIndex = Array.from(header.parentNode.children).indexOf(header);

                    // Reset des autres en-têtes
                    headers.forEach(function (h) {
                        if (h !== header) {
                            h.dataset.order = 'asc';
                            const otherArrow = h.querySelector('.sort-arrow');
                            if (otherArrow) {
                                otherArrow.textContent = '▲';
                            }
                        }
                    });

                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    rows.sort(function (rowA, rowB) {
                        const cellA = rowA.children[columnIndex].innerText.trim();
                        const cellB = rowB.children[columnIndex].innerText.trim();

                        let a = cellA;
                        let b = cellB;

                        if (type === 'number') {
                            // Remplacement virgule/espaces pour conversion
                            a = parseFloat(cellA.replace(/[^0-9,.-]/g, '').replace(',', '.')) || 0;
                            b = parseFloat(cellB.replace(/[^0-9,.-]/g, '').replace(',', '.')) || 0;
                        } else if (type === 'date') {
                            a = new Date(cellA);
                            b = new Date(cellB);
                        } else { // string
                            a = cellA.toLowerCase();
                            b = cellB.toLowerCase();
                        }

                        if (a < b) return currentOrder === 'asc' ? -1 : 1;
                        if (a > b) return currentOrder === 'asc' ? 1 : -1;
                        return 0;
                    });

                    // Réinjection des lignes dans le DOM
                    rows.forEach(function (row) {
                        tbody.appendChild(row);
                    });

                    // Met à jour la flèche et l'ordre
                    header.dataset.order = currentOrder;
                    const arrow = header.querySelector('.sort-arrow');
                    if (arrow) {
                        arrow.textContent = currentOrder === 'asc' ? '▲' : '▼';
                    }
                });
            });
        });
    </script>
</body>
</html>
