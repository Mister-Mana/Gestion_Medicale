<?php
// auth.php
function register($conn, $input)
{
    $nom = $conn->real_escape_string($input['nom']);
    $prenom = $conn->real_escape_string($input['prenom']);
    $email = $conn->real_escape_string($input['email']);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($input['role']);

    $sql = "INSERT INTO users (nom, prenom, email, password, role) VALUES ('$nom', '$prenom', '$email', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Utilisateur enregistré avec succès']);
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'enregistrement: ' . $conn->error]);
    }
}

function login($conn, $input)
{
    $email = $conn->real_escape_string($input['email']);
    $password = $input['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(16));
            $user_id = $user['id'];
            $sql = "UPDATE users SET token = '$token' WHERE id = $user_id";
            $conn->query($sql);
            echo json_encode(['token' => $token, 'user_id' => $user_id, 'role' => $user['role']]);
        } else {
            echo json_encode(['error' => 'Mot de passe incorrect']);
        }
    } else {
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    }
}

function authenticate($conn)
{
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $sql = "SELECT * FROM users WHERE token = '$token'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
    }
    return false;
}

?>