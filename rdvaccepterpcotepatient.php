<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurer les en-têtes HTTP
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Inclure la connexion à la base de données
include "dbconnect.php"; 

// Vérifier si la connexion à la base de données est bien établie
// if (!$conn) {
//     echo json_encode(['error' => 'Erreur de connexion à la base de données']);
//     exit();
// }

// Vérifier si l'ID du patient est bien reçu
if (!isset($_GET['id_patient'])) {
    echo json_encode(['error' => 'ID du patient non fourni']);
    exit();
}

$id_patient = $_GET['id_patient'];

// Correction de la requête SQL avec des jointures valides
$sql = "
    SELECT 
        u.prenom AS prenom_medecin, 
        u.nom AS nom_medecin, 
        h.mydate, 
        h.heure 
    FROM 
        historique h
    JOIN 
        medecin m ON h.id_medecin = m.id_medecin
    JOIN
        user u ON u.id_user = m.id_user
    WHERE 
        h.id_patient = ?;
";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
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

// Fermer la connexion
$stmt->close();
$conn->close();
?>
