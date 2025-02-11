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
if (!isset($data['email']) || !isset($data['motdepasse'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email ou mot de passe manquant.'
    ]);
    exit();
}

$email = trim($data['email']);
$motdepasse = trim($data['motdepasse']);

try {
    // Requête SQL pour vérifier si l'utilisateur existe
    $query = "SELECT * FROM user WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);

    // Exécuter la requête avec des paramètres sécurisés
    $stmt->execute(['email' => $email]);

    // Récupérer les résultats
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Vérifier si le mot de passe est correct
        if (password_verify($motdepasse, $user['mot_de_passe'])) {
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Connexion réussie.',
                'nom' => $user['nom'], // Informations supplémentaires si nécessaire
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
} catch (PDOException $e) {
    // Gérer les erreurs liées à la base de données
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur interne : ' . $e->getMessage()
    ]);
}
?>
