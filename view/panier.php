<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }

        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .cart-container { display: flex; gap: 20px; flex-wrap: wrap; }
        .cart-items { flex: 2; min-width: 300px; }
        .cart-summary { flex: 1; min-width: 280px; }

        .cart-item {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .cart-item-info { flex: 1; }
        .cart-item-info h3 { margin: 0 0 5px 0; color: #333; }
        .cart-item-info .type { color: #666; font-size: 0.9em; }
        .cart-item-info .price { color: #28a745; font-weight: bold; font-size: 1.1em; }
        .cart-item-info .price-original { text-decoration: line-through; color: #999; font-size: 0.9em; }
        .cart-item-info .promo-badge { background: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8em; }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .quantity-control input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .quantity-control button {
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .quantity-control button:hover { background: #0056b3; }

        .btn-remove {
            padding: 8px 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-remove:hover { background: #c82333; }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        .summary-box h2 { margin-top: 0; color: #333; }
        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-line.total {
            font-size: 1.3em;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #333;
            border-bottom: none;
            margin-top: 10px;
            padding-top: 15px;
        }
        .summary-line .label { color: #666; }
        .summary-line .value { font-weight: bold; }
        .free-shipping { color: #28a745; font-weight: bold; }
        .shipping-info { font-size: 0.85em; color: #666; margin-top: 5px; }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn-primary { background-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; }
        .btn-success:hover { background-color: #218838; }
        .btn-warning { background-color: #ffc107; color: #333; }
        .btn-warning:hover { background-color: #e0a800; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }

        .empty-cart {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .empty-cart h2 { color: #666; }

        .cart-badge {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8em;
            margin-left: 5px;
        }

        .subtotal { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Mon Panier <span class="cart-badge"><?= $resume['nombre_articles'] ?> article(s)</span></h1>

        <?php if (isset($_SESSION['panier_success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['panier_success']) ?>
            </div>
            <?php unset($_SESSION['panier_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['panier_error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['panier_error']) ?>
            </div>
            <?php unset($_SESSION['panier_error']); ?>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <a href="index.php?action=produits" class="btn btn-primary">← Continuer mes achats</a>
            <a href="index.php" class="btn btn-secondary">Accueil</a>
            <?php if ($resume['nombre_articles'] > 0): ?>
                <a href="index.php?action=panier&subaction=vider" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment vider le panier ?')">Vider le panier</a>
            <?php endif; ?>
        </div>

        <?php if ($resume['nombre_articles'] === 0): ?>
            <div class="empty-cart">
                <h2>Votre panier est vide</h2>
                <p>Ajoutez des produits depuis notre catalogue pour commencer vos achats.</p>
                <a href="index.php?action=produits" class="btn btn-success">Voir les produits</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <?php foreach ($resume['articles'] as $item): ?>
                        <?php
                            $prixUnitaire = Panier::getPrixAvecPromo($item['prix_ht'], $item['promo']);
                            $sousTotal = $prixUnitaire * $item['quantite'];
                        ?>
                        <div class="cart-item" data-id="<?= $item['id'] ?>">
                            <img src="<?= !empty($item['image']) ? htmlspecialchars($item['image']) : 'uploads/default.jpeg' ?>"
                                 alt="<?= htmlspecialchars($item['designation']) ?>">

                            <div class="cart-item-info">
                                <h3><?= htmlspecialchars($item['designation']) ?></h3>
                                <div class="type"><?= htmlspecialchars($item['type']) ?></div>

                                <?php if ($item['promo'] > 0): ?>
                                    <div>
                                        <span class="price-original"><?= number_format($item['prix_ht'], 2, ',', ' ') ?> €</span>
                                        <span class="promo-badge">-<?= $item['promo'] ?>%</span>
                                    </div>
                                    <div class="price"><?= number_format($prixUnitaire, 2, ',', ' ') ?> € HT</div>
                                <?php else: ?>
                                    <div class="price"><?= number_format($item['prix_ht'], 2, ',', ' ') ?> € HT</div>
                                <?php endif; ?>

                                <div class="subtotal">
                                    Sous-total: <strong><?= number_format($sousTotal, 2, ',', ' ') ?> € HT</strong>
                                </div>
                            </div>

                            <form method="POST" action="index.php?action=panier&subaction=modifier" class="quantity-control">
                                <input type="hidden" name="id_produit" value="<?= $item['id'] ?>">
                                <input type="number" name="quantite" value="<?= $item['quantite'] ?>"
                                       min="1" max="<?= $item['stock'] ?>"
                                       onchange="this.form.submit()">
                                <button type="submit">Mettre à jour</button>
                            </form>

                            <a href="index.php?action=panier&subaction=supprimer&id=<?= $item['id'] ?>"
                               class="btn-remove"
                               onclick="return confirm('Retirer ce produit du panier ?')">
                                🗑️
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-box">
                        <h2>Récapitulatif</h2>

                        <div class="summary-line">
                            <span class="label">Sous-total HT</span>
                            <span class="value"><?= number_format($resume['sous_total_ht'], 2, ',', ' ') ?> €</span>
                        </div>

                        <div class="summary-line">
                            <span class="label">TVA (<?= $resume['tva_taux'] ?>%)</span>
                            <span class="value"><?= number_format($resume['tva_montant'], 2, ',', ' ') ?> €</span>
                        </div>

                        <div class="summary-line">
                            <span class="label">Frais d'expédition</span>
                            <?php if ($resume['frais_expedition'] == 0 && $resume['nombre_articles'] > 0): ?>
                                <span class="value free-shipping">GRATUIT</span>
                            <?php else: ?>
                                <span class="value"><?= number_format($resume['frais_expedition'], 2, ',', ' ') ?> €</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($resume['frais_expedition'] > 0): ?>
                            <div class="shipping-info">
                                💡 Livraison gratuite à partir de <?= number_format($resume['seuil_livraison_gratuite'], 2, ',', ' ') ?> € HT
                                <br>
                                Il vous manque <strong><?= number_format($resume['seuil_livraison_gratuite'] - $resume['sous_total_ht'], 2, ',', ' ') ?> €</strong>
                            </div>
                        <?php endif; ?>

                        <div class="summary-line total">
                            <span class="label">Total TTC</span>
                            <span class="value"><?= number_format($resume['total_ttc'], 2, ',', ' ') ?> €</span>
                        </div>

                        <div style="margin-top: 20px;">
                            <button class="btn btn-success" style="width: 100%; font-size: 1.1em;" onclick="alert('Fonctionnalité de paiement à implémenter')">
                                Procéder au paiement
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Mise à jour dynamique (optionnel - pour AJAX)
        function updateCartSummary() {
            fetch('index.php?action=panier&subaction=json')
                .then(response => response.json())
                .then(data => {
                    console.log('Panier mis à jour:', data);
                });
        }
    </script>
</body>
</html>
