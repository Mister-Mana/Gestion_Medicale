<?php
class Rendezvous
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function checkAvailability($medecin_id, $date_rendezvous)
    {
        $stmt = $this->mysqli->prepare('SELECT * FROM disponibilites_medecins WHERE medecin_id = ? AND date_disponibilite = ?');
        $stmt->bind_param('is', $medecin_id, $date_rendezvous);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        // Vérifier la disponibilité du médecin
        $disponibilites = $this->checkAvailability($data['medecin_id'], date('Y-m-d', strtotime($data['date_rendezvous'])));

        // Vérifier les conflits d'horaire
        foreach ($disponibilites as $disponibilite) {
            $heure_debut = $disponibilite['heure_debut'];
            $heure_fin = $disponibilite['heure_fin'];
            $rendezvous_heure = date('H:i:s', strtotime($data['date_rendezvous']));

            if ($rendezvous_heure >= $heure_debut && $rendezvous_heure <= $heure_fin) {
                // Le créneau est disponible
                break;
            }
        }

        // Si aucune disponibilité correspondante n'est trouvée, retournez une erreur
        if (empty($disponibilites)) {
            return ['error' => 'Médecin indisponible à ce moment.'];
        }

        // Créer le rendez-vous
        $stmt = $this->mysqli->prepare('INSERT INTO rendezvous (patient_id, medecin_id, service_id, date_rendezvous, statut) VALUES (?, ?, ?, ?, ?)');
        $statut = 'en_attente'; // Par défaut, le statut est en attente
        $stmt->bind_param('iiiss', $data['patient_id'], $data['medecin_id'], $data['service_id'], $data['date_rendezvous'], $statut);
        $stmt->execute();
        return ['id' => $stmt->insert_id];
    }

    public function read($id)
    {
        $stmt = $this->mysqli->prepare('SELECT r.*, s.nom_service, p.nom AS patient_nom, m.nom AS medecin_nom FROM rendezvous r JOIN services_medicals s ON r.service_id = s.id JOIN utilisateurs p ON r.patient_id = p.id JOIN utilisateurs m ON r.medecin_id = m.id WHERE r.id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($id, $data)
    {
        $stmt = $this->mysqli->prepare('UPDATE rendezvous SET patient_id = ?, medecin_id = ?, service_id = ?, date_rendezvous = ?, statut = ? WHERE id = ?');
        $stmt->bind_param('iiissi', $data['patient_id'], $data['medecin_id'], $data['service_id'], $data['date_rendezvous'], $data['statut'], $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->mysqli->prepare('DELETE FROM rendezvous WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function listByMedecin($medecinId)
    {
        $stmt = $this->mysqli->prepare('SELECT r.*, s.nom_service, p.nom AS patient_nom FROM rendezvous r JOIN services_medicals s ON r.service_id = s.id JOIN utilisateurs p ON r.patient_id = p.id WHERE r.medecin_id = ?');
        $stmt->bind_param('i', $medecinId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
}

?>