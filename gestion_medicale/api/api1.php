<?php
// api1.php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'config.php';
require_once 'auth.php';
require_once 'rendezvous.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

function authenticate()
{
    // Implémentation de l'authentification
    // Retourne les informations de l'utilisateur si authentifié, sinon false
}

$endpoint = $request[0] ?? '';
$id = $request[1] ?? null;

switch ($endpoint) {
    case 'register':
        if ($method === 'POST') {
            register($conn, $input);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
        }
        break;

    case 'login':
        if ($method === 'POST') {
            login($conn, $input);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
        }
        break;

    case 'rendezvous':
        $user = authenticate();
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Non autorisé"]);
            break;
        }

        switch ($method) {
            case 'GET':
                getRendezVous($conn, $user);
                break;
            case 'POST':
                if ($user['role'] === 'patient') {
                    createRendezVous($conn, $user, $input);
                } else {
                    http_response_code(403);
                    echo json_encode(["error" => "Accès refusé"]);
                }
                break;
            case 'PUT':
                if ($user['role'] === 'patient' && $id) {
                    updateRendezVous($conn, $user, $input, $id);
                } else {
                    http_response_code(403);
                    echo json_encode(["error" => "Accès refusé ou ID manquant"]);
                }
                break;
            case 'DELETE':
                if ($user['role'] === 'patient' && $id) {
                    deleteRendezVous($conn, $user, $id);
                } else {
                    http_response_code(403);
                    echo json_encode(["error" => "Accès refusé ou ID manquant"]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["error" => "Méthode non autorisée"]);
                break;
        }
        break;

    case 'agenda':
        $user = authenticate();
        if (!$user || $user['role'] !== 'medecin') {
            http_response_code(403);
            echo json_encode(["error" => "Accès refusé"]);
            break;
        }

        if ($method === 'GET') {
            getAgenda($conn, $user);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
        }
        break;

    case 'confirm':
        $user = authenticate();
        if (!$user || $user['role'] !== 'medecin') {
            http_response_code(403);
            echo json_encode(["error" => "Accès refusé"]);
            break;
        }

        if ($method === 'PUT' && $id) {
            confirmRendezVous($conn, $user, $input, $id);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée ou ID manquant"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint non trouvé"]);
        break;
}

$conn->close();
?>