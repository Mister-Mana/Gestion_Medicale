<?php
session_start();
require 'config.php';

function authenticateUser($telephone, $mot_de_passe, $role)
{
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM utilisateurs WHERE telephone = ? AND role = ?');
    $stmt->bind_param('ss', $telephone, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $utilisateur = $result->fetch_assoc();
        if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            return $utilisateur;
        } else {
            return null; // Mot de passe invalide
        }
    }
    return null; // Utilisateur non trouvé
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $telephone = $data['telephone'];
    $mot_de_passe = $data['mot_de_passe'];
    $role = $data['role']; // 'medecin' ou 'patient'

    $utilisateur = authenticateUser($telephone, $mot_de_passe, $role);
    if ($utilisateur) {
        echo json_encode(['message' => 'Connexion avec succès', 'user' => $utilisateur]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Informations invalides']);
    }
}
