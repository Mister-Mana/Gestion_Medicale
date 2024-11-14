<?
// Fichier pour la déconnexion
session_start();
session_destroy();
header('Location: /'); // Rediriger vers la page d'accueil ou de connexion

?>