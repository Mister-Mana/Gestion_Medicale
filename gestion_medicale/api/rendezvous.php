<?php
// rendezvous.php
function getRendezVous($conn, $user)
{
    if ($user['role'] == 'patient') {
        $sql = "SELECT * FROM rendezvous WHERE patient_id = {$user['id']}";
    } elseif ($user['role'] == 'medecin') {
        $sql = "SELECT * FROM rendezvous WHERE medecin_id = {$user['id']}";
    } else {
        echo json_encode(['error' => 'Rôle non autorisé']);
        return;
    }

    $result = $conn->query($sql);
    $rendezvous = [];
    while ($row = $result->fetch_assoc()) {
        $rendezvous[] = $row;
    }
    echo json_encode($rendezvous);
}

function createRendezVous($conn, $user, $input)
{
    if ($user['role'] != 'patient') {
        echo json_encode(['error' => 'Seuls les patients peuvent demander un rendez-vous']);
        return;
    }

    $patient_id = $user['id'];
    $medecin_id = $conn->real_escape_string($input['medecin_id']);
    $date = $conn->real_escape_string($input['date']);
    $heure = $conn->real_escape_string($input['heure']);
    $motif = $conn->real_escape_string($input['motif']);

    $sql = "INSERT INTO rendezvous (patient_id, medecin_id, date, heure, motif, statut) 
            VALUES ($patient_id, $medecin_id, '$date', '$heure', '$motif', 'en_attente')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Rendez-vous demandé avec succès']);
    } else {
        echo json_encode(['error' => 'Erreur lors de la demande de rendez-vous: ' . $conn->error]);
    }
}

function updateRendezVous($conn, $user, $input, $id)
{
    if ($user['role'] != 'patient') {
        echo json_encode(['error' => 'Seuls les patients peuvent modifier un rendez-vous']);
        return;
    }

    $date = $conn->real_escape_string($input['date']);
    $heure = $conn->real_escape_string($input['heure']);
    $motif = $conn->real_escape_string($input['motif']);

    $sql = "UPDATE rendezvous SET date = '$date', heure = '$heure', motif = '$motif', statut = 'en_attente' 
            WHERE id = $id AND patient_id = {$user['id']}";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Rendez-vous mis à jour avec succès']);
    } else {
        echo json_encode(['error' => 'Erreur lors de la mise à jour du rendez-vous: ' . $conn->error]);
    }
}

function deleteRendezVous($conn, $user, $id)
{
    if ($user['role'] != 'patient') {
        echo json_encode(['error' => 'Seuls les patients peuvent annuler un rendez-vous']);
        return;
    }

    $sql = "DELETE FROM rendezvous WHERE id = $id AND patient_id = {$user['id']}";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Rendez-vous annulé avec succès']);
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'annulation du rendez-vous: ' . $conn->error]);
    }
}

function getAgenda($conn, $user)
{
    $sql = "SELECT r.*, u.nom as patient_nom, u.prenom as patient_prenom 
            FROM rendezvous r 
            JOIN users u ON r.patient_id = u.id 
            WHERE r.medecin_id = {$user['id']} 
            ORDER BY r.date, r.heure";

    $result = $conn->query($sql);
    $agenda = [];
    while ($row = $result->fetch_assoc()) {
        $agenda[] = $row;
    }
    echo json_encode($agenda);
}

function confirmRendezVous($conn, $user, $input, $id)
{
    $statut = $conn->real_escape_string($input['statut']);

    $sql = "UPDATE rendezvous SET statut = '$statut' 
            WHERE id = $id AND medecin_id = {$user['id']}";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Statut du rendez-vous mis à jour avec succès']);
    } else {
        echo json_encode(['error' => 'Erreur lors de la mise à jour du statut: ' . $conn->error]);
    }
}

?>