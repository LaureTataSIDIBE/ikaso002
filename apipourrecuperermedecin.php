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
    // Récupérer tous les utilisateurs de type "medecin"
    $query = "SELECT * FROM user WHERE type_user = 'medecin'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $insertedCount = 0; // Compteur pour les médecins insérés

    foreach ($medecins as $medecin) {
        // Vérifier si l'id_user existe déjà dans la table medecin 
        $checkQuery = "SELECT COUNT(*) FROM medecin WHERE id_user = :id_user";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(['id_user' => $medecin['id_user']]);
        $exists = $checkStmt->fetchColumn();
    
        if ($exists == 0) {
            // Insérer chaque médecin dans la table medecin sans id_medecin
            $insertQuery = "INSERT INTO medecin (id_user) VALUES (:id_user)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute(['id_user' => $medecin['id_user']]);
            $insertedCount++;

            // Message de débogage
            // echo "Médecin avec id_user {$medecin['id_user']} inséré.<br>";
        }
    }

    // Réponse finale
    echo json_encode([
        'success' => true,
        'message' => $insertedCount > 0 ? "$insertedCount médecin(s) inséré(s) avec succès." : "Aucun médecin à insérer."
    ]);
} catch (PDOException $e) {
    // Gérer les erreurs liées à la base de données
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne : ' . $e->getMessage()
    ]);
}
?>