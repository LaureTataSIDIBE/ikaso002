<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Connexion à la base de données
include "dbconnect.php";
// Requête SQL pour récupérer les médecins
$sql = "SELECT m.id_medecin, u.nom, u.prenom, m.specialisation
        FROM user u
        INNER JOIN medecin m ON u.id_user = m.id_user
        WHERE u.type_user = 'medecin'";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Retourner les résultats sous forme JSON
$medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($medecins);
?>
