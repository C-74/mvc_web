<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Historique.php';

class ProduitController {
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new Produit($db);
    }

    public function index() {
        $typeFiltre = $_GET['type'] ?? '';
        $typesDisponibles = $this->model->lireTypes();

        if ($typeFiltre !== '') {
            $produits = $this->model->lireParType($typeFiltre);
        } else {
            $produits = $this->model->lireTous();
        }

        $historique = new Historique($this->model->getDbConnection());
        $historiquePrix = $historique->lireTous();
        include __DIR__ . '/../view/produit_liste.php';
    }

    public function create() {
        $erreur = ''; // Pour stocker les messages d'erreur
        $type_val = $_POST['type'] ?? '';
        $designation_val = $_POST['designation'] ?? '';
        $prix_val = $_POST['prix'] ?? '';
        $promo_val = $_POST['promo'] ?? '';
        $date_arrive_val = $_POST['date_arrive'] ?? '';
        $stock_val = $_POST['stock'] ?? '';

        if ($_POST) {
            $type = trim($_POST['type']);
            $designation = trim($_POST['designation']);
            $prix = $_POST['prix'];
            $promo = $_POST['promo'] ?? '';
            $date_arrive = $_POST['date_arrive'];
            $stock = $_POST['stock'];

            // Validation côté serveur
            if ($type === '' || $designation === '') {
                $erreur = "Tous les champs doivent être remplis.";
            } elseif (!is_numeric($prix) || $prix < 0.01) {
                $erreur = "Le prix doit être au minimum de 0,01 €.";
            } elseif ($promo !== '' && (!is_numeric($promo) || $promo < 0 || $promo > 100)) {
                $erreur = "La promotion doit être un pourcentage entre 0 et 100.";
            } elseif (strtotime($date_arrive) < strtotime(date('Y-m-d'))) {
                $erreur = "La date d'arrivée doit être aujourd'hui ou ultérieure.";            
            } elseif (!preg_match('/^[1-9]\d*$/', $stock)) {
                $erreur = "Stock invalide, il doit être un entier positif sans symboles.";
            }

            if ($erreur === '') {
                // Gestion de l'image (optionnelle)
                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $basename = basename($_FILES['image']['name']);
                        $targetPath = $uploadDir . $basename;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                            $imagePath = 'uploads/' . $basename;
                        } else {
                            $erreur = "Erreur lors du téléversement de l'image.";
                        }
                    } else {
                        $erreur = "Erreur lors du téléversement de l'image.";
                    }
                }

                if ($erreur === '') {
                    $this->model->pro_type = $type;
                    $this->model->pro_designation = $designation;
                    $this->model->pro_prix_ht = $prix;
                    $this->model->pro_promo = ($promo === '' ? null : $promo);
                    $this->model->pro_date_arrive = $date_arrive;
                    $this->model->pro_stock = $stock;
                    $this->model->pro_image = $imagePath;
                    $this->model->creer();
                    header("Location: index.php?action=produits");
                    exit;
                }
            }            
        }

        include __DIR__ . '/../view/produit_form.php';
    }

    public function edit($id) {
        $this->model->pro_idproduit = $id;
        $erreur = ''; // Pour stocker les messages d'erreur

        // Récupération du produit existant pour pré-remplir le formulaire
        $produit = $this->model->lireUn();
        $imageActuelle = $produit['pro_image'] ?? null;
        $prixActuel = $produit['pro_prix_ht'] ?? null; 

        if ($_POST) {
            $type = trim($_POST['type']);
            $designation = trim($_POST['designation']);
            $prix = $_POST['prix'];
            $promo = $_POST['promo'] ?? '';
            $date_arrive = $_POST['date_arrive'];
            $stock = $_POST['stock'];

            // Validation côté serveur
            if ($type === '' || $designation === '') {
                $erreur = "Tous les champs doivent être remplis.";
            } elseif (!is_numeric($prix) || $prix < 0.01) {
                $erreur = "Le prix doit être au minimum de 0,01 €.";
            } elseif ($promo !== '' && (!is_numeric($promo) || $promo < 0 || $promo > 100)) {
                $erreur = "La promotion doit être un pourcentage entre 0 et 100.";
            } elseif (strtotime($date_arrive) < strtotime(date('Y-m-d'))) {
                $erreur = "La date d'arrivée doit être aujourd'hui ou ultérieure.";            
            } elseif (!preg_match('/^[1-9]\d*$/', $stock)) {
                $erreur = "Stock invalide, il doit être un entier positif sans symboles.";
            }

            if ($erreur === '') {
                // Gestion de l'image (optionnelle)
                $imagePath = $imageActuelle;
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $basename = basename($_FILES['image']['name']);
                        $targetPath = $uploadDir . $basename;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                            $imagePath = 'uploads/' . $basename;
                        } else {
                            $erreur = "Erreur lors du téléversement de l'image.";
                        }
                    } else {
                        $erreur = "Erreur lors du téléversement de l'image.";
                    }
                }

                if ($erreur === '') {
                    // Si le prix a changé, enregistrer dans l'historique
                    if ($prix != $prixActuel) {
                        $historiqueModel = new Historique($this->model->getDbConnection());
                        $historiqueModel->hpr_idproduit = $id;
                        $historiqueModel->hpr_prix_ht = $prixActuel;
                        $historiqueModel->creer();
                    }

                    $this->model->pro_type = $type;
                    $this->model->pro_designation = $designation;
                    $this->model->pro_prix_ht = $prix;
                    $this->model->pro_promo = ($promo === '' ? null : $promo);
                    $this->model->pro_date_arrive = $date_arrive;
                    $this->model->pro_stock = $stock;
                    $this->model->pro_image = $imagePath;
                    $this->model->modifier();
                    header("Location: index.php?action=produits");
                    exit;
                }
            } else {           
                // Récupérer les valeurs du formulaire pour les réafficher en cas d'erreur
                $produit = [
                    'pro_type' => $type,
                    'pro_designation' => $designation,
                    'pro_prix_ht' => $prix,
                    'pro_date_arrive' => $date_arrive,
                    'pro_stock' => $stock,
                    'pro_promo' => $promo,
                    'pro_image' => $imageActuelle,
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
