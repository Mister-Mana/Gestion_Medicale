<?php
// users.php
require 'config.php';

function emailExists($mysqli, $email)
{
    $email = $mysqli->real_escape_string($email);
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $mysqli->query($sql);
    return $result->num_rows > 0;
}

function register($mysqli, $input)
{
    $nom = $mysqli->real_escape_string($input['nom']);
    $prenom = $mysqli->real_escape_string($input['prenom']);
    $email = $mysqli->real_escape_string($input['email']);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);
    $role = $mysqli->real_escape_string($input['role']);
    $service = isset($input['service']) ? $mysqli->real_escape_string($input['service']) : null;

    if (emailExists($mysqli, $email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Cet email est déjà utilisé']);
        return;
    }

    $sql = "INSERT INTO users (nom, prenom, email, password, role, service) 
            VALUES ('$nom', '$prenom', '$email', '$password', '$role', " . ($service ? "'$service'" : "NULL") . ")";

    if ($mysqli->query($sql) === TRUE) {
        $user_id = $mysqli->insert_id;
        $token = bin2hex(random_bytes(16));

        $update_token_sql = "UPDATE users SET token = '$token' WHERE id = $user_id";
        $mysqli->query($update_token_sql);

        http_response_code(201);
        echo json_encode([
            'message' => 'Utilisateur enregistré avec succès',
            'user_id' => $user_id,
            'token' => $token
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de l\'enregistrement: ' . $$mysqli->error]);
    }
}

function login($mysqli, $input)
{
    $email = $$mysqli->real_escape_string($input['email']);
    $password = $input['password'];

    $sql = "SELECT id, password, role FROM users WHERE email = '$email'";
    $result = $mysqli->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(16));
            $user_id = $user['id'];

            $update_token_sql = "UPDATE users SET token = '$token' WHERE id = $user_id";
            $mysqli->query($update_token_sql);

            http_response_code(200);
            echo json_encode([
                'message' => 'Authentification réussie',
                'user_id' => $user_id,
                'role' => $user['role'],
                'token' => $token
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Mot de passe incorrect']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    }
}

function authenticate($mysqli)
{
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $token = $mysqli->real_escape_string($token);

        $sql = "SELECT id, nom, prenom, email, role, service FROM users WHERE token = '$token'";
        $result = $mysqli->query($sql);

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
    }
    return false;
}
?>