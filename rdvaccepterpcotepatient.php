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

if (isset($_POST['id_patient'])) {
    $id_patient = intval($_POST['id_patient']);
    
    // Préparer la requête SQL pour obtenir les rendez-vous acceptés
    $stmt = $conn->prepare("
        SELECT 
            r.id_medecin, 
            m.nom AS nom_medecin, 
            m.prenom AS prenom_medecin, 
            r.mydate, 
            r.heure 
        FROM 
            rendezvous r
        JOIN 
            medecin m ON r.id_medecin = m.id_medecin
        WHERE 
            r.id_patient = ? AND r.statut = 'accepté'
    ");
    
    $stmt->bind_param("i", $id_patient);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $rendezvous = [];

        while ($row = $result->fetch_assoc()) {
            $rendezvous[] = $row;
        }

        echo json_encode(['rendezvous' => $rendezvous]);
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
    }
} else {
    echo json_encode(['error' => 'ID du patient non fourni']);
}

$conn->close();
?>