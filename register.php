<?php
// Définir l'en-tête pour la réponse JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Activer les erreurs pour le débogage (désactivez ceci en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de connexion à la base de données
include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

// Récupérer les données envoyées depuis Flutter
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs nécessaires sont envoyés
if (!isset($data['email'], $data['mot_de_passe'], $data['type_user'], $data['nom'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Tous les champs obligatoires ne sont pas fournis.',
    ]);
    exit();
}

$email = trim($data['email']);
$mot_de_passe = trim($data['mot_de_passe']);
$type_user = trim($data['type_user']); // patient, medecin ou admin
$nom = trim($data['nom']);

try {
    // Vérifier si l'email existe déjà dans la base de données
    $query = "SELECT * FROM user WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cet email est déjà enregistré.',
        ]);
        exit();
    }

    // Insérer le nouvel utilisateur dans la base de données
    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    $query = "INSERT INTO user (email, mot_de_passe, type_user, nom) VALUES (:email, :mot_de_passe, :type_user, :nom)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'email' => $email,
        'mot_de_passe' => $hashed_password,
        'type_user' => $type_user,
        'nom' => $nom,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Inscription réussie.',
        'type_user' => $type_user,
    ]);
} catch (PDOException $e) {
    // Gérer les erreurs liées à la base de données
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne : ' . $e->getMessage(),
    ]);
}
?>
