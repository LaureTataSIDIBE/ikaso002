<?php
// Configuration de la connexion à la base de données
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "dbconnect.php"; 

if (isset($_GET['id_medecin'])) {
    $id_medecin = $_GET['id_medecin'];
    
    // Préparer la requête SQL pour obtenir les rendez-vous acceptés
    $stmt =  $pdo->prepare("
       SELECT 
            u.prenom, 
            u.nom, 
            h.mydate, 
            h.heure 
        FROM 
            historique h
        JOIN 
            patient p ON h.id_patient = p.id_patient
        JOIN
            user u ON u.id_user = p.id_user
        WHERE 
            h.id_medecin = :id_medecin;
                
    ");
    

    
    if ($stmt->execute([
        ':id_medecin' => $_GET['id_medecin']
    ])) {
        // Récupérer les résultats correctement avec PDO
        $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        echo json_encode(['rendezvous' => $rendezvous]);
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'exécution de la requête']);
    }    
} else {
    echo json_encode(['error' => 'ID du médecin non fourni']);
}
?>