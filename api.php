<?php
require_once 'user.php';
require_once 'consultation.php';

header('Content-Type: application/json');

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Exemple pour gérer les utilisateurs
if ($_GET['endpoint'] === 'users') {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                echo json_encode(getUserById($_GET['id']));
            } else {
                echo json_encode(['error' => 'ID requis']);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = addUser($data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'], $data['type_user'], $data['telephone'], $data['adresse']);
            echo json_encode(['message' => 'Utilisateur ajouté', 'id' => $id]);
            break;

        default:
            echo json_encode(['error' => 'Méthode non supportée']);
    }
}
?>
