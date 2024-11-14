<?php
require 'config.php';
require 'Rendezvous.php';
require 'ServicesMedicals.php';

header('Content-Type: application/json');

$rendezvousModel = new Rendezvous($mysqli);
$servicesModel = new ServicesMedicals($mysqli);

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($requestUri[0] === 'rendezvous') {
    switch ($method) {
        case 'GET':
            if (isset($requestUri[1])) {
                // Lire un rendez-vous par ID
                $rendezvous = $rendezvousModel->read($requestUri[1]);
                echo json_encode($rendezvous);
            } else {
                // Liste des rendez-vous pour un médecin spécifique (ID passé comme paramètre)
                $medecinId = $_GET['medecin_id'] ?? null;
                if ($medecinId) {
                    $rendezvousList = $rendezvousModel->listByMedecin($medecinId);
                    echo json_encode($rendezvousList);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing medecin_id parameter']);
                }
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $rendezvousModel->create($data);
            if (isset($result['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
            } else {
                echo json_encode(['id' => $result['id']]);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $rendezvousModel->update($requestUri[1], $data);
            echo json_encode(['message' => 'Rendez-vous mis à jour.']);
            break;

        case 'DELETE':
            $rendezvousModel->delete($requestUri[1]);
            echo json_encode(['message' => 'Rendez-vous supprimé.']);
            break;

        default:
            http_response_code(405);
            break;
    }

    // Ajoutez le code suivant dans votre switch pour gérer les services médicaux
    if ($requestUri[0] === 'services') {
        switch ($method) {
            case 'GET':
                if (isset($requestUri[1])) {
                    // Lire un service par ID
                    $service = $servicesModel->read($requestUri[1]);
                    echo json_encode($service);
                } else {
                    // Lister tous les services
                    $servicesList = $servicesModel->list();
                    echo json_encode($servicesList);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $servicesModel->create($data);
                echo json_encode(['id' => $id]);
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $servicesModel->update($requestUri[1], $data);
                echo json_encode(['message' => 'Service mis à jour.']);
                break;

            case 'DELETE':
                $servicesModel->delete($requestUri[1]);
                echo json_encode(['message' => 'Service supprimé.']);
                break;

            default:
                http_response_code(405);
                break;
        }
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Aucune information n\'a été trouvé']);
}

?>