<?php
$host = 'localhost';
$db = 'gestion_medicale';
$user = 'root'; // Remplacez par votre utilisateur
$pass = ''; // Remplacez par votre mot de passe

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>