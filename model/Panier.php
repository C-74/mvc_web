<?php
class Panier {
    private $conn;
    private $idUtilisateur;
    private $idPanier;

    // Constantes pour les calculs
    const TVA_RATE = 20.0; // 20% TVA
    const FRAIS_EXPEDITION_BASE = 5.99; // Frais de base
    const FRAIS_EXPEDITION_PAR_ARTICLE = 1.50; // Par article supplémentaire
    const SEUIL_LIVRAISON_GRATUITE = 100.0; // Livraison gratuite au-dessus de ce montant HT

    public function __construct($db) {
        $this->conn = $db;
        $this->idUtilisateur = $_SESSION['user']->uti_idutilisateur ?? null;
        if ($this->idUtilisateur) {
            $this->idPanier = $this->getOrCreatePanier();
        }
    }

    /**
     * Récupère ou crée le panier de l'utilisateur
     */
    private function getOrCreatePanier() {
        // Chercher un panier existant
        $query = "SELECT pan_idpanier FROM t_panier_pan WHERE pan_idutilisateur = :id_user";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $this->idUtilisateur);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['pan_idpanier'];
        }

        // Créer un nouveau panier
        $query = "INSERT INTO t_panier_pan (pan_idutilisateur) VALUES (:id_user)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $this->idUtilisateur);
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Ajoute un produit au panier
     */
    public function ajouter($idProduit, $quantite = 1) {
        if (!$this->idPanier) {
            return ['error' => 'Utilisateur non connecté'];
        }

        // Vérifier que le produit existe et récupérer ses infos
        $produit = $this->getProduitInfo($idProduit);
        if (!$produit) {
            return ['error' => 'Produit introuvable'];
        }

        // Vérifier si le produit est déjà dans le panier
        $query = "SELECT lpa_idligne, lpa_quantite FROM t_ligne_panier_lpa
                  WHERE lpa_idpanier = :id_panier AND lpa_idproduit = :id_produit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->bindParam(':id_produit', $idProduit);
        $stmt->execute();
        $ligneExistante = $stmt->fetch(PDO::FETCH_ASSOC);

        $nouvelleQuantite = $ligneExistante ? $ligneExistante['lpa_quantite'] + $quantite : $quantite;

        // Vérifier le stock disponible
        if ($nouvelleQuantite > $produit['pro_stock']) {
            return ['error' => 'Stock insuffisant. Stock disponible: ' . $produit['pro_stock']];
        }

        if ($ligneExistante) {
            // Mettre à jour la quantité
            $query = "UPDATE t_ligne_panier_lpa
                      SET lpa_quantite = :quantite
                      WHERE lpa_idligne = :id_ligne";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantite', $nouvelleQuantite);
            $stmt->bindParam(':id_ligne', $ligneExistante['lpa_idligne']);
            $stmt->execute();
        } else {
            // Ajouter une nouvelle ligne
            $query = "INSERT INTO t_ligne_panier_lpa
                      (lpa_idpanier, lpa_idproduit, lpa_quantite, lpa_prix_unitaire, lpa_promo)
                      VALUES (:id_panier, :id_produit, :quantite, :prix, :promo)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_panier', $this->idPanier);
            $stmt->bindParam(':id_produit', $idProduit);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':prix', $produit['pro_prix_ht']);
            $stmt->bindParam(':promo', $produit['pro_promo']);
            $stmt->execute();
        }

        // Mettre à jour la date de modification du panier
        $this->updatePanierTimestamp();

        return true;
    }

    /**
     * Met à jour la quantité d'un produit
     */
    public function modifierQuantite($idProduit, $quantite) {
        if (!$this->idPanier) {
            return ['error' => 'Utilisateur non connecté'];
        }

        if ($quantite <= 0) {
            return $this->supprimer($idProduit);
        }

        // Vérifier le stock
        $produit = $this->getProduitInfo($idProduit);
        if (!$produit) {
            return ['error' => 'Produit introuvable'];
        }

        if ($quantite > $produit['pro_stock']) {
            return ['error' => 'Stock insuffisant. Stock disponible: ' . $produit['pro_stock']];
        }

        $query = "UPDATE t_ligne_panier_lpa
                  SET lpa_quantite = :quantite, lpa_prix_unitaire = :prix, lpa_promo = :promo
                  WHERE lpa_idpanier = :id_panier AND lpa_idproduit = :id_produit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':prix', $produit['pro_prix_ht']);
        $stmt->bindParam(':promo', $produit['pro_promo']);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->bindParam(':id_produit', $idProduit);
        $stmt->execute();

        $this->updatePanierTimestamp();

        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime un produit du panier
     */
    public function supprimer($idProduit) {
        if (!$this->idPanier) {
            return false;
        }

        $query = "DELETE FROM t_ligne_panier_lpa
                  WHERE lpa_idpanier = :id_panier AND lpa_idproduit = :id_produit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->bindParam(':id_produit', $idProduit);
        $stmt->execute();

        $this->updatePanierTimestamp();

        return $stmt->rowCount() > 0;
    }

    /**
     * Vide complètement le panier
     */
    public function vider() {
        if (!$this->idPanier) {
            return false;
        }

        $query = "DELETE FROM t_ligne_panier_lpa WHERE lpa_idpanier = :id_panier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->execute();

        $this->updatePanierTimestamp();

        return true;
    }

    /**
     * Met à jour le timestamp de modification du panier
     */
    private function updatePanierTimestamp() {
        $query = "UPDATE t_panier_pan SET pan_date_modification = CURRENT_TIMESTAMP WHERE pan_idpanier = :id_panier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->execute();
    }

    /**
     * Récupère le contenu du panier avec les infos produits
     */
    public function getContenu() {
        if (!$this->idPanier) {
            return [];
        }

        $query = "SELECT
                    lpa.lpa_idligne,
                    lpa.lpa_idproduit as id,
                    lpa.lpa_quantite as quantite,
                    lpa.lpa_prix_unitaire as prix_ht,
                    lpa.lpa_promo as promo,
                    lpa.lpa_date_ajout,
                    p.pro_designation as designation,
                    p.pro_type as type,
                    p.pro_image as image,
                    p.pro_stock as stock
                  FROM t_ligne_panier_lpa lpa
                  INNER JOIN t_produit_pro p ON lpa.lpa_idproduit = p.pro_idproduit
                  WHERE lpa.lpa_idpanier = :id_panier
                  ORDER BY lpa.lpa_date_ajout DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total d'articles
     */
    public function getNombreArticles() {
        if (!$this->idPanier) {
            return 0;
        }

        $query = "SELECT COALESCE(SUM(lpa_quantite), 0) as total
                  FROM t_ligne_panier_lpa
                  WHERE lpa_idpanier = :id_panier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'];
    }

    /**
     * Calcule le prix unitaire avec promo
     */
    public static function getPrixAvecPromo($prixHT, $promo) {
        if ($promo > 0) {
            return $prixHT * (1 - $promo / 100);
        }
        return $prixHT;
    }

    /**
     * Calcule le sous-total HT (sans TVA ni frais)
     */
    public function getSousTotalHT() {
        $contenu = $this->getContenu();
        $total = 0;
        foreach ($contenu as $item) {
            $prixUnitaire = self::getPrixAvecPromo($item['prix_ht'], $item['promo']);
            $total += $prixUnitaire * $item['quantite'];
        }
        return round($total, 2);
    }

    /**
     * Calcule la TVA
     */
    public function getTVA() {
        $sousTotalHT = $this->getSousTotalHT();
        return round($sousTotalHT * (self::TVA_RATE / 100), 2);
    }

    /**
     * Calcule les frais d'expédition
     */
    public function getFraisExpedition() {
        $sousTotalHT = $this->getSousTotalHT();

        // Livraison gratuite si le montant dépasse le seuil
        if ($sousTotalHT >= self::SEUIL_LIVRAISON_GRATUITE) {
            return 0;
        }

        $nbArticles = $this->getNombreArticles();
        if ($nbArticles === 0) {
            return 0;
        }

        // Frais de base + frais par article supplémentaire
        $frais = self::FRAIS_EXPEDITION_BASE + (($nbArticles - 1) * self::FRAIS_EXPEDITION_PAR_ARTICLE);
        return round($frais, 2);
    }

    /**
     * Calcule le total TTC (HT + TVA + Frais)
     */
    public function getTotalTTC() {
        $sousTotalHT = $this->getSousTotalHT();
        $tva = $this->getTVA();
        $frais = $this->getFraisExpedition();
        return round($sousTotalHT + $tva + $frais, 2);
    }

    /**
     * Récupère les informations d'un produit depuis la base
     */
    private function getProduitInfo($idProduit) {
        $query = "SELECT * FROM t_produit_pro WHERE pro_idproduit = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idProduit);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne un résumé complet du panier
     */
    public function getResume() {
        return [
            'articles' => $this->getContenu(),
            'nombre_articles' => $this->getNombreArticles(),
            'sous_total_ht' => $this->getSousTotalHT(),
            'tva_taux' => self::TVA_RATE,
            'tva_montant' => $this->getTVA(),
            'frais_expedition' => $this->getFraisExpedition(),
            'seuil_livraison_gratuite' => self::SEUIL_LIVRAISON_GRATUITE,
            'total_ttc' => $this->getTotalTTC()
        ];
    }

    /**
     * Récupère les informations du panier (dates, etc.)
     */
    public function getInfoPanier() {
        if (!$this->idPanier) {
            return null;
        }

        $query = "SELECT * FROM t_panier_pan WHERE pan_idpanier = :id_panier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->idPanier);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
