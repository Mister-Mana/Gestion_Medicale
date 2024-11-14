<?php
require 'config.php';

header("content-type:text/plain");

$sessionId = $_POST['sessionId'];
$serviceCode = $_POST['serviceCode'];
$phoneNumber = $_POST['phoneNumber'];

$text = $_POST['text'];

if ($text == "") {
    $response = "Bienvenue à la Gestion des Rendez-vous Médicaux\n";
    $response .= "1. Authentification Patient\n";
    $response .= "2. Authentification Médecin\n";
    $response .= "3. Prendre un Rendez-vous\n";
    $response .= "0. Quitter";
} else if ($text == "1") {
    $response = "Entrez votre numéro de téléphone :";
} else if (preg_match("/^1\*([0-9]+)$/", $text, $matches)) {
    $telephone = $matches[1];
    $response = "Entrez votre mot de passe :";
} else if (preg_match("/^1\*[0-9]+\*([0-9]+)$/", $text, $matches)) {
    $mot_de_passe = $matches[1];
    $data = ['telephone' => $telephone, 'mot_de_passe' => $mot_de_passe, 'role' => 'patient'];
    $ch = curl_init('localhost/gestion_medicale/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($result, true);
    if (isset($result['message'])) {
        $response = "FIN " . $result['message'];
    } else {
        $response = "FIN " . $result['error'];
    }
} else if ($text == "3") {
    $ch = curl_init('localhost/gestion_medicale/services');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $services = json_decode($result, true);
    $response = "Choisissez un service médical :\n";
    foreach ($services as $service) {
        $response .= $service['id'] . ". " . $service['nom_service'] . "\n";
    }
    $response .= "0. Quitter";
} else if (preg_match("/^3\*([0-9]+)$/", $text, $matches)) {
    $serviceId = $matches[1];
    $response = "Entrez votre numéro de téléphone :";
} else if (preg_match("/^3\*[0-9]+\*([0-9]+)$/", $text, $matches)) {
    $telephone = $matches[1];
    $response = "Choisissez un médecin :\n";
    
    // Récupérer et afficher les médecins disponibles ici
    $ch = curl_init('localhost/gestion_medicale/rendezvous?medecin_id={id}'); // Remplacez {id} par l'ID du médecin
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $medecins = json_decode($result, true);
    foreach ($medecins as $medecin) {
        $response .= $medecin['id'] . ". " . $medecin['nom'] . "\n";
    }
    $response .= "0. Quitter";
} else if (preg_match("/^3\*[0-9]+\*[0-9]+$/", $text, $matches)) {
    $parts = explode('*', $text);
    $serviceId = $parts[1];
    $medecinId = $parts[2];
    $response = "Entrez la date et l'heure du rendez-vous (YYYY-MM-DD HH:MM) :";
} else if (preg_match("/^3\*[0-9]+\*[0-9]+\*(.+)$/", $text, $matches)) {
    $dateRendezvous = $matches[1];
    $data = [
        'patient_id' => $patientId, // Remplacer par l'ID du patient authentifié
        'medecin_id' => $medecinId,
        'service_id' => $serviceId,
        'date_rendezvous' => $dateRendezvous
    ];
    
    $ch = curl_init('localhost/gestion_medicale/rendezvous'); // Remplacez par l'URL de votre API
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($result, true);
    if (isset($result['error'])) {
        $response = "FIN " . $result['error'];
    } else {
        $response = "FIN Rendez-vous pris avec succès. ID : " . $result['id'];
    }
} else {
    $response = "FIN Option invalide.";
}

header('Content-type: text/plain');
echo $response;
?>