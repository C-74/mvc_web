<?php
// Fonction pour vérifier si l'user est bien connecté
function requireAuth() {
    if (!isset($_SESSION['user'])) {
        // Si il n'est pas connecté, on le redirige vers la page de login
        header("Location: index.php?action=login");
        exit;
    }
}

// Fonction pour vérifier si l'user est admin
function requireAdmin() {
    if (!isset($_SESSION['user']) || !$_SESSION['user']->uti_admin) {
        // Si il n'est pas admin, on le redirige vers la page d'accueil
        header("Location: index.php");
        exit;
    }
}