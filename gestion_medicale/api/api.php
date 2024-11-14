<?php
// api.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'config.php';
require_once 'auth.php';
require_once 'users.php';
require_once 'rendezvous.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

$endpoint = $request[0] ?? '';

switch ($endpoint) {
    case 'register':
        if ($method == 'POST') {
            register($conn, $input);
        }
        break;
    case 'login':
        if ($method == 'POST') {
            login($conn, $input);
        }
        break;
    case 'rendezvous':
        $user = authenticate($conn);
        if (!$user) {
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        switch ($method) {
            case 'GET':
                getRendezVous($conn, $user);
                break;
            case 'POST':
                createRendezVous($conn, $user, $input);
                break;
            case 'PUT':
                updateRendezVous($conn, $user, $input, $request[1] ?? null);
                break;
            case 'DELETE':
                deleteRendezVous($conn, $user, $request[1] ?? null);
                break;
        }
        break;
    case 'agenda':
        $user = authenticate($conn);
        if (!$user || $user['role'] != 'medecin') {
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        getAgenda($conn, $user);
        break;
    case 'confirm':
        $user = authenticate($conn);
        if (!$user || $user['role'] != 'medecin') {
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        if ($method == 'PUT') {
            confirmRendezVous($conn, $user, $input, $request[1] ?? null);
        }
        break;
    default:
        echo json_encode(['error' => 'Endpoint non valide']);
        break;
}

$conn->close();

?>