<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connexion à la base de données
include "dbconnect.php"; // Assurez-vous que ce fichier initialise un objet $pdo

$id_medecin = $_GET['id_medecin'] ?? null;

if ($id_medecin === null) {
    http_response_code(400);
    echo json_encode(["error" => "ID médecin requis"]);
    exit;
}

try {
    // Préparer la requête pour récupérer les rendez-vous avec les noms des patients et du médecin
    $query = "
        SELECT 
            p.nom AS patient_name, 
            p.prenom AS patient_surname, 
            r.mydate, 
            r.heure,
            u.nom AS doctor_name,
            u.prenom AS doctor_surname
        FROM rendezvous r
        JOIN patient p ON r.id_patient = p.id_patient
        JOIN user u ON p.id_user = u.id_user
        WHERE r.id_medecin = :id_medecin
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les résultats
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Réponse JSON
    echo json_encode($appointments);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de la récupération des rendez-vous : " . $e->getMessage()]);
}
?>