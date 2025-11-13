<?php
require_once 'controller/UtilisateurController.php';
require_once 'controller/ProduitController.php';
require_once 'controller/AuthController.php';
require_once __DIR__ . '/config/auth.php';

$action = $_GET['action'] ?? null;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

switch($action) {
    case 'login':
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit();
        }
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit();
        }
        $controller = new AuthController();
        // Vous devrez implémenter la méthode register dans AuthController
        $controller->register();
        break;
    
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'utilisateurs':
        requireAuth(); // Vérifie si l'utilisateur est connecté
        $controller = new UtilisateurController();
        $subaction = $_GET['subaction'] ?? 'index';
        $id = $_GET['id'] ?? null;
        switch($subaction) {
            case 'create': 
            requireAdmin(); // Vérifie si l'utilisateur est admin
            $controller->create(); 
            break;
            case 'edit': 
            requireAdmin(); // Vérifie si l'utilisateur est admin
            $controller->edit($id); 
            break;
            case 'delete': 
            requireAdmin(); // Vérifie si l'utilisateur est admin
            $controller->delete($id); 
            break;
            default: $controller->index(); break;
        }
        break;

    case 'produits':
        requireAuth(); // Vérifie si l'utilisateur est connecté
        $controller = new ProduitController();
        $subaction = $_GET['subaction'] ?? 'index';
        $id = $_GET['id'] ?? null;
        switch($subaction) {
            case 'create': 
            requireAdmin(); // Vérifie si l'utilisateur est admin
            $controller->create(); 
            break;
            case 'edit': 
            requireAdmin(); // Vérifie si l'utilisateur est admin
            $controller->edit($id); 
            break;
            case 'delete': 
            requireAdmin(); // Vérifie si l'utilisateur est admin
            $controller->delete($id); 
            break;
            default: 
            $controller->index(); 
            break;
        }
        break;

    default:
        requireAuth(); // Vérifie si l'utilisateur est connecté
        include 'view/accueil.php';
        break;
}
