<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connexion à la base de données
include "dbconnect.php"; 

if (isset($_GET['id_medecin'])) {
    $id_medecin = $_GET['id_medecin'];

    try {
        $query = "SELECT * FROM rendezvous WHERE id_medecin = :id_medecin ORDER BY mydate, heure";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
        $stmt->execute();
        $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($rendezvous);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de la récupération : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Paramètre id_medecin manquant."]);
}
?>
