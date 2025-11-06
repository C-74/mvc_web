<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../model/Utilisateur.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    public function login() {
        $success = $_GET['success'] ?? null; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Connexion BDD
            $database = new Database();
            $db = $database->getConnection();

            // Passez la connexion au modèle
            $utilisateurModel = new Utilisateur($db);
            $user = $utilisateurModel->getUser($username);
            
            if ($user && password_verify($password, $user->uti_mdp)) {
                $_SESSION['user'] = $user;
                $utilisateurModel->updateLastConnection($user->uti_idutilisateur);
                header("Location: index.php");
                exit;
            } else {
                $erreur = "Identifiants invalides";
            }
        }
        include __DIR__ . '/../view/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if ($password !== $password_confirm) {
                $erreur = "Les mots de passe ne correspondent pas.";
                include __DIR__ . '/../view/signup.php';
                return;
            }

            // Connexion BDD
            $database = new Database();
            $db = $database->getConnection();

            // Passez la connexion au modèle
            $utilisateurModel = new Utilisateur($db);

            // Vérifiez si l'utilisateur existe déjà
            if ($utilisateurModel->getUser($username)) {
                $erreur = "Le nom d'utilisateur existe déjà.";
            } else {
                // Créez le nouvel utilisateur
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $utilisateurModel->createUser($username, $email, $hashedPassword);
                $success = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
                header("Location: index.php?action=login&success=" . urlencode($success));
                exit;
            }
        }
        include __DIR__ . '/../view/signup.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
