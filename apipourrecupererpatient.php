<?php
session_start();
// Définir l'en-tête pour la réponse JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Inclure le fichier de connexion à la base de données
include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

try {
    // Récupérer tous les utilisateurs de type "patient"
    $query = "SELECT * FROM user WHERE type_user = 'patient'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($patients as $patient) {
        // Vérifier si l'id_user existe déjà dans la table patient
        $checkQuery = "SELECT COUNT(*) FROM patient WHERE id_user = :id_user";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(['id_user' => $patient['id_user']]);
        $exists = $checkStmt->fetchColumn();
    
        if ($exists == 0) {
            // Insérer chaque patient dans la table patient sans id_patient
            $insertQuery = "INSERT INTO patient (id_user) VALUES (:id_user)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute(['id_user' => $patient['id_user']]);
        }
    }
    echo json_encode([
        'success' => true,
        'message' => 'Patients insérés avec succès.'
    ]);
} catch (PDOException $e) {
    // Gérer les erreurs liées à la base de données
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne : ' . $e->getMessage()
    ]);
}
?>