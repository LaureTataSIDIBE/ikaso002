<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "dbconnect.php"; // Assurez-vous que ce fichier initialise un objet $pdo

$input = json_decode(file_get_contents('php://input'), true);
$id_user = $input['id_user'];
$old_password = $input['old_password'];
$new_password = $input['new_password'];

// Connexion à la base de données
$conn = new mysqli('localhost', 'username', 'password', 'ikaso001');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Échec de la connexion à la base de données.']));
}

// Vérifier l'ancien mot de passe
$sql = "SELECT mot_de_passe FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($old_password, $user['mot_de_passe'])) {
    echo json_encode(['error' => 'Ancien mot de passe incorrect.']);
    exit;
}

// Hachage du nouveau mot de passe
$new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Changer le mot de passe
$sql = "UPDATE user SET mot_de_passe = ? WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_password_hashed, $id_user);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Mot de passe changé avec succès.']);
} else {
    echo json_encode(['error' => 'Erreur lors du changement de mot de passe.']);
}

$stmt->close();
$conn->close();
?>