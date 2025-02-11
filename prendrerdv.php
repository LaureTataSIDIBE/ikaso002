<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connexion à la base de données
include "dbconnect.php"; 

// Vérification des paramètres GET
if (isset($_GET['id_patient'], $_GET['id_medecin'], $_GET['mydate'], $_GET['heure'])) {
    $id_patient = $_GET['id_patient'];
    $id_medecin = $_GET['id_medecin'];
    $mydate = $_GET['mydate'];
    $heure = $_GET['heure'];

    // Date et heure actuelles pour la prise du rendez-vous
    $dateDePrise = date('Y-m-d'); 
    $heureDePrise = date('H:i'); 

    try {
        // Insérer le rendez-vous dans la base
        $query = "INSERT INTO rendezvous (id_patient, id_medecin, mydate, heure, datedeprise, heuredeprise) 
                  VALUES (:id_patient, :id_medecin, :mydate, :heure, :datedeprise, :heuredeprise)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
        $stmt->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
        $stmt->bindParam(':mydate', $mydate, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $heure, PDO::PARAM_STR);
        $stmt->bindParam(':datedeprise', $dateDePrise, PDO::PARAM_STR);
        $stmt->bindParam(':heuredeprise', $heureDePrise, PDO::PARAM_STR);
        $stmt->execute();

        // Réponse JSON succès
        echo json_encode(["success" => "Rendez-vous enregistré avec succès."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de l'enregistrement : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Paramètres manquants (id_patient, id_medecin, mydate, heure)."]);
}
?>
