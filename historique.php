<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "dbconnect.php";

// $data = json_decode(file_get_contents('php://input'), true);
// var_dump($data);
// // echo "Données brutes reçues : " . $data ;

// // Vérification des paramètres obligatoires
// $required = ['id_medecin', 'id_patient', 'mydate', 'heure'];
// foreach ($required as $param) {
//     if (!isset($data[$param]) || empty($data[$param])) {
//         http_response_code(400);
//         // echo json_encode(["error" => "Parametre manquant: $param"]);
//         exit;
//     }
// }

try {
    // Insertion dans la table historique
    $stmt = $pdo->prepare("
        INSERT INTO historique 
        (id_medecin, id_patient, mydate, heure, datedeprise, heuredeprise) 
        VALUES (:id_medecin, :id_patient, :mydate, :heure, CURDATE(), CURTIME())
    ");

    $stmt->execute([
        ':id_medecin' => $_POST['id_medecin'],
        ':id_patient' => $_POST['id_patient'],
        ':mydate' => $_POST['mydate'],
        ':heure' => $_POST['heure']
    ]);

    if ($stmt->rowCount() > 0) {
        // Suppression d'un rendez-vous
        $stmt = $pdo->prepare("
            DELETE FROM rendezvous 
            WHERE id_medecin = :id_medecin 
            AND id_patient = :id_patient 
            AND mydate = :mydate 
            AND heure = :heure
        ");

        $stmt->execute([
            ':id_medecin' => $_POST['id_medecin'],
            ':id_patient' => $_POST['id_patient'],
            ':mydate' => $_POST['mydate'],
            ':heure' => $_POST['heure']
        ]);

        echo json_encode(["success" => "Rendez-vous enregistré avec succès et ancien rendez-vous supprimé."]);
    } else {
        echo json_encode(["error" => "Aucune modification effectuée lors de l'enregistrement."]);
    }
}catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur base de données: " . $e->getMessage()]);
}
?>