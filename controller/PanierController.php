<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Panier.php';

class PanierController {
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new Panier($db);
    }

    /**
     * Affiche le panier
     */
    public function index() {
        $resume = $this->model->getResume();
        $panierModel = $this->model; // Pour la vue
        include __DIR__ . '/../view/panier.php';
    }

    /**
     * Ajoute un produit au panier
     */
    public function ajouter() {
        $idProduit = $_POST['id_produit'] ?? $_GET['id'] ?? null;
        $quantite = $_POST['quantite'] ?? 1;

        if ($idProduit) {
            $result = $this->model->ajouter($idProduit, (int)$quantite);

            if (is_array($result) && isset($result['error'])) {
                $_SESSION['panier_error'] = $result['error'];
            } else {
                $_SESSION['panier_success'] = 'Produit ajouté au panier';
            }
        }

        // Rediriger vers la page précédente ou le panier
        $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php?action=panier';
        header("Location: " . $redirect);
        exit;
    }

    /**
     * Met à jour la quantité d'un produit
     */
    public function modifier() {
        $idProduit = $_POST['id_produit'] ?? null;
        $quantite = $_POST['quantite'] ?? 1;

        if ($idProduit) {
            $result = $this->model->modifierQuantite($idProduit, (int)$quantite);

            if (is_array($result) && isset($result['error'])) {
                $_SESSION['panier_error'] = $result['error'];
            } else {
                $_SESSION['panier_success'] = 'Quantité mise à jour';
            }
        }

        header("Location: index.php?action=panier");
        exit;
    }

    /**
     * Supprime un produit du panier
     */
    public function supprimer() {
        $idProduit = $_GET['id'] ?? $_POST['id_produit'] ?? null;

        if ($idProduit) {
            $this->model->supprimer($idProduit);
            $_SESSION['panier_success'] = 'Produit retiré du panier';
        }

        header("Location: index.php?action=panier");
        exit;
    }

    /**
     * Vide le panier
     */
    public function vider() {
        $this->model->vider();
        $_SESSION['panier_success'] = 'Panier vidé';
        header("Location: index.php?action=panier");
        exit;
    }

    /**
     * Retourne les données du panier en JSON (pour AJAX)
     */
    public function getJson() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getResume());
        exit;
    }

    /**
     * Retourne le nombre d'articles (utile pour les vues)
     */
    public function getNombreArticles() {
        return $this->model->getNombreArticles();
    }
}

