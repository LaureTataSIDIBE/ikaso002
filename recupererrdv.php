<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include "dbconnect.php";// Inclure la configuration de la base de données

// Vérifier si l'ID du médecin est passé dans la requête
if (isset($_GET['id_medecin'])) {
    $id_medecin = $_GET['id_medecin'];

    // Récupérer les rendez-vous du médecin spécifique
    $query = "SELECT * FROM rendezvous WHERE id_medecin = '$id_medecin'";
    $result = mysqli_query($conn, $query);

    $rendezvous = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rendezvous[] = $row;
    }

    echo json_encode(['rendezvous' => $rendezvous]);
} else {
    echo json_encode(['error' => 'ID du médecin manquant']);
}
?>
