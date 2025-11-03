<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Utilisateur.php';

class UtilisateurController {
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new Utilisateur($db);
    }

    public function index() {
        $utilisateurs = $this->model->lireTous();
        include __DIR__ . '/../view/utilisateur_liste.php';
    }

    public function create() {
        $erreur = ''; // Pour stocker les messages d'erreur
        $login_val = $_POST['login'] ?? '';
        $mdp_val = $_POST['mdp'] ?? '';
        $mdp_conf_val = $_POST['mdp_conf'] ?? '';
        $mail_val = $_POST['mail'] ?? '';
        $idcompte_val = $_POST['idcompte'] ?? '';

        if ($_POST) {
            $login = trim($_POST['login']);
            $mdp = trim($_POST['mdp']);
            $mdp_conf = trim($_POST['mdp_conf'] ?? '');
            $mail = trim($_POST['mail']);
            $idcompte = $_POST['idcompte'];

            // Validation côté serveur
            if ($login === '' || $mdp === '' || $mail === '' || preg_match('/\s/', $login.$mdp.$mail)) {
                $erreur = "Tous les champs doivent être remplis et ne doivent pas contenir d'espaces.";
            } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $mail)) {
                $erreur = "Email invalide. Veuillez entrer un email de type exemple@exemple.com.";
            } elseif (!preg_match('/^[1-9]\d*$/', $idcompte)) {
                $erreur = "ID Compte invalide, il doit être un entier positif sans symboles.";
            } elseif ($this->model->loginExiste($login)) {
                $erreur = "Ce login est déjà utilisé.";
            } elseif (strlen($mdp) < 8) {
                $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
            } elseif ($mdp !== $mdp_conf) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } elseif ($this->model->mailExiste($mail)) {
                $erreur = "Cet email est déjà utilisé.";
            } elseif ($this->model->idCompteExiste($idcompte)) {
                $erreur = "Cet ID Compte est déjà utilisé.";
            }

            // Si pas d'erreur, insertion
            if ($erreur === '') {
                $this->model->uti_login = $login;
                $this->model->uti_mdp = password_hash($mdp, PASSWORD_DEFAULT);
                $this->model->uti_idcompte = $idcompte;
                $this->model->uti_mail = $mail;
                $this->model->creer();
                header("Location: index.php?action=utilisateurs");
                exit;
            }
        }

        include __DIR__ . '/../view/utilisateur_form.php';
    }

    public function edit($id) {
        $this->model->uti_idutilisateur = $id;
        $erreur = ''; // Pour stocker les messages d'erreur

        // Récupération de l'utilisateur existant pour pré-remplir le formulaire
        $utilisateur = $this->model->lireUn();

        if ($_POST) {
            $login = trim($_POST['login']);
            $mdp = trim($_POST['mdp']);
            $mdp_conf = trim($_POST['mdp_conf'] ?? '');
            $mail = trim($_POST['mail']);

            // Validation côté serveur
            if ($login === '' || $mail === '' || preg_match('/\s/', $login.$mail)) {
                $erreur = "Le login et l'email doivent être remplis et ne pas contenir d'espaces.";
            } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $mail)) {
                $erreur = "Email invalide. Veuillez entrer un email de type exemple@exemple.com.";
            }
            // Vérification du login déjà existant chez un autre utilisateur
            elseif ($this->model->loginExistePourAutre($login, $id)) {
                $erreur = "Ce login est déjà utilisé.";
            }
            // Vérification du mail déjà existant chez un autre utilisateur
            elseif ($this->model->mailExistePourAutre($mail, $id)) {
                $erreur = "Cet email est déjà utilisé.";
            }

            // Si pas d'erreur, mise à jour
            if ($erreur === '') {
                $this->model->uti_login = $login;
                $this->model->uti_mail = $mail;
                // Mettre à jour le mot de passe seulement s’il a été saisi
                if ($mdp !== '' || $mdp_conf !== '') {
                    if (strlen($mdp) < 8) {
                        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
                        include __DIR__ . '/../view/utilisateur_edit.php';
                        return;                        
                    } elseif ($mdp !== $mdp_conf) {
                        $erreur = "Les mots de passe ne correspondent pas.";
                        include __DIR__ . '/../view/utilisateur_edit.php';
                        return;
                    }
                    $this->model->uti_mdp = password_hash($mdp, PASSWORD_DEFAULT);
                } else {
                    // On garde l'ancien mot de passe
                    $this->model->uti_mdp = $utilisateur['uti_mdp'];
                }
                $this->model->modifier();
                header("Location: index.php?action=utilisateurs");
                exit;
            } else {
                // Récupérer les valeurs du formulaire pour les réafficher en cas d'erreur
                $utilisateur = [
                    'uti_login' => $login,
                    'uti_mail' => $mail,
                ];
            }
        }

        include __DIR__ . '/../view/utilisateur_edit.php';
    }


    public function delete($id) {
        $this->model->uti_idutilisateur = $id;
        $this->model->supprimer();
        header("Location: index.php?action=utilisateurs");
    }
}
