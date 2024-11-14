<?php
class ServicesMedicals
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function create($data)
    {
        $stmt = $this->mysqli->prepare('INSERT INTO services_medicals (nom_service, description) VALUES (?, ?)');
        $stmt->bind_param('ss', $data['nom_service'], $data['description']);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function read($id)
    {
        $stmt = $this->mysqli->prepare('SELECT * FROM services_medicals WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($id, $data)
    {
        $stmt = $this->mysqli->prepare('UPDATE services_medicals SET nom_service = ?, description = ? WHERE id = ?');
        $stmt->bind_param('ssi', $data['nom_service'], $data['description'], $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->mysqli->prepare('DELETE FROM services_medicals WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function list()
    {
        $stmt = $this->mysqli->prepare('SELECT * FROM services_medicals');
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>