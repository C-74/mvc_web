<?php
require_once 'controller/UtilisateurController.php';
require_once 'controller/ProduitController.php';

$action = $_GET['action'] ?? null;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

switch($action) {
    case 'utilisateurs':
        $controller = new UtilisateurController();
        $subaction = $_GET['subaction'] ?? 'index';
        $id = $_GET['id'] ?? null;
        switch($subaction) {
            case 'create': $controller->create(); break;
            case 'edit': $controller->edit($id); break;
            case 'delete': $controller->delete($id); break;
            default: $controller->index(); break;
        }
        break;

    case 'produits':
        $controller = new ProduitController();
        $subaction = $_GET['subaction'] ?? 'index';
        $id = $_GET['id'] ?? null;
        switch($subaction) {
            case 'create': $controller->create(); break;
            case 'edit': $controller->edit($id); break;
            case 'delete': $controller->delete($id); break;
            default: $controller->index(); break;
        }
        break;

    default:
        include 'view/accueil.php';
        break;
}
