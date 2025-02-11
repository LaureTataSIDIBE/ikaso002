<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "dbconnect.php"; // Assurez-vous que ce fichier initialise un objet $pdo

try {
    $userId = $_GET['id_user']; // ID de l'utilisateur

    $stmt = $pdo->prepare("SELECT nom, prenom, email FROM user WHERE id_user = :id_user");
    $stmt->bindParam(':id_user', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(["error" => "Utilisateur non trouvé."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors du traitement de la requête : " . $e->getMessage()]);
}
?>