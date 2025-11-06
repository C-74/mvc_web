<?php
// Fonction pour vérifier si l'user est bien connecté
function requireAuth() {
    if (!isset($_SESSION['user'])) {
        // Si il n'est pas connecté, on le redirige vers la page de login
        header("Location: index.php?action=login");
        exit;
    }
}