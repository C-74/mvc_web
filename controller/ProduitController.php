<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Produit.php';

class ProduitController {
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new Produit($db);
    }

    public function index() {
        $produits = $this->model->lireTous();
        include __DIR__ . '/../view/produit_liste.php';
    }

    public function create() {
        $erreur = ''; // Pour stocker les messages d'erreur
        $type_val = $_POST['type'] ?? '';
        $designation_val = $_POST['designation'] ?? '';
        $prix_val = $_POST['prix'] ?? '';
        $date_arrive_val = $_POST['date_arrive'] ?? '';
        $stock_val = $_POST['stock'] ?? '';

        if ($_POST) {
            $type = trim($_POST['type']);
            $designation = trim($_POST['designation']);
            $prix = $_POST['prix'];
            $date_arrive = $_POST['date_arrive'];
            $stock = $_POST['stock'];

            // Validation côté serveur
            if ($type === '' || $designation === '') {
                $erreur = "Tous les champs doivent être remplis.";
            } elseif (!is_numeric($prix) || $prix < 0.01) {
                $erreur = "Le prix doit être au minimum de 0,01 €.";
            } elseif (strtotime($date_arrive) < strtotime(date('Y-m-d'))) {
                $erreur = "La date d'arrivée doit être aujourd'hui ou ultérieure.";            
            } elseif (!preg_match('/^[1-9]\d*$/', $stock)) {
                $erreur = "Stock invalide, il doit être un entier positif sans symboles.";
            }

            // Si pas d'erreur, insertion
            if ($erreur === '') {
                $this->model->pro_type = $type;
                $this->model->pro_designation = $designation;
                $this->model->pro_prix_ht = $prix;
                $this->model->pro_date_arrive = $date_arrive;
                $this->model->pro_stock = $stock;
                $this->model->creer();
                header("Location: index.php?action=produits");
                exit;
            }            
        }

        include __DIR__ . '/../view/produit_form.php';
    }

    public function edit($id) {
        $this->model->pro_idproduit = $id;
        $erreur = ''; // Pour stocker les messages d'erreur

        // Récupération du produit existant pour pré-remplir le formulaire
        $produit = $this->model->lireUn();

        if ($_POST) {
            $type = trim($_POST['type']);
            $designation = trim($_POST['designation']);
            $prix = $_POST['prix'];
            $date_arrive = $_POST['date_arrive'];
            $stock = $_POST['stock'];

            // Validation côté serveur
            if ($type === '' || $designation === '') {
                $erreur = "Tous les champs doivent être remplis.";
            } elseif (!is_numeric($prix) || $prix < 0.01) {
                $erreur = "Le prix doit être au minimum de 0,01 €.";
            } elseif (strtotime($date_arrive) < strtotime(date('Y-m-d'))) {
                $erreur = "La date d'arrivée doit être aujourd'hui ou ultérieure.";            
            } elseif (!preg_match('/^[1-9]\d*$/', $stock)) {
                $erreur = "Stock invalide, il doit être un entier positif sans symboles.";
            }

            // Si pas d'erreur, mise à jour
            if ($erreur === '') {
                $this->model->pro_type = $type;
                $this->model->pro_designation = $designation;
                $this->model->pro_prix_ht = $prix;
                $this->model->pro_date_arrive = $date_arrive;
                $this->model->pro_stock = $stock;
                $this->model->modifier();
                header("Location: index.php?action=produits");
                exit;
            } else {           
                // Récupérer les valeurs du formulaire pour les réafficher en cas d'erreur
                $produit = [
                    'pro_type' => $type,
                    'pro_designation' => $designation,
                    'pro_prix_ht' => $prix,
                    'pro_date_arrive' => $date_arrive,
                    'pro_stock' => $stock,
                ];
            }
        }

        include __DIR__ . '/../view/produit_edit.php';
    }

    public function delete($id) {
        $this->model->pro_idproduit = $id;
        $this->model->supprimer();
        header("Location: index.php?action=produits");
    }
}
