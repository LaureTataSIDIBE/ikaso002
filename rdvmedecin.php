<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Inclure le fichier de connexion à la base de données
include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

// Vérifiez si l'ID du médecin est passé en tant que paramètre GET
if (!isset($_GET['id_medecin'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID du médecin manquant.'
    ]);
    exit();
}

$medecinId = intval(trim($_GET['id_medecin']));

try {
    // Requête SQL pour récupérer les rendez-vous du médecin
    $query = "SELECT r.id_rendezvous, u.nom AS patient_nom, u.prenom AS patient_prenom, r.mydate, r.heure 
              FROM rendezvous r 
              JOIN patient p ON r.id_patient = p.id_patient 
              JOIN user u ON p.id_user = u.id_user 
              WHERE r.id_medecin = :id_medecin";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_medecin' => $medecinId]);

    // Récupérer les résultats
    $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'rendezvous' => $rendezvous
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne : ' . $e->getMessage()
    ]);
}
?>