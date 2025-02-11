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
include('dbconnect.php'); // Remplacez par le nom exact de votre fichier de connexion

// Récupérer les données envoyées depuis Flutter
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs nécessaires sont envoyés
if (!isset($data['email']) || !isset($data['motdepasse'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email ou mot de passe manquant.'
    ]);
    exit();
}

$email = trim($data['email']);
$motdepasse = trim($data['motdepasse']);

// Connexion à la base de données
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$connection) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de connexion à la base de données.'
    ]);
    exit();
}

// Requête SQL pour vérifier si l'utilisateur existe
$query = "SELECT * FROM utilisateurs WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Vérification de l'utilisateur
if ($user = mysqli_fetch_assoc($result)) {
    // Vérifier si le mot de passe est correct
    if (password_verify($motdepasse, $user['motdepasse'])) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Connexion réussie.',
            'nom' => $user['nom'],  // Informations supplémentaires si nécessaire
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Mot de passe incorrect.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utilisateur non trouvé avec cet email.'
    ]);
}

// Fermer la connexion à la base de données
mysqli_close($connection);
?>
