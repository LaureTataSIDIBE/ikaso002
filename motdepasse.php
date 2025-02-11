<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connexion à la base de données
include "dbconnect.php"; // Assurez-vous que ce fichier initialise un objet $pdo

try {
    // Récupérer les données de la requête
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['id_user']; // ID de l'utilisateur
    $oldPassword = $data['old_password']; // Ancien mot de passe
    $newPassword = $data['new_password']; // Nouveau mot de passe

    // Vérifier que les champs ne sont pas vides
    if (empty($userId) || empty($oldPassword) || empty($newPassword)) {
        echo json_encode(["error" => "Tous les champs sont requis."]);
        exit();
    }

    // Vérifier l'ancien mot de passe
    $stmt = $pdo->prepare("SELECT mod_de_passe FROM user WHERE id_user = :id_user");
    $stmt->bindParam(':id_user', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($oldPassword, $result['mod_de_passe'])) {
        // Mettre à jour le mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE user SET mod_de_passe = :new_password WHERE id_user = :id_user");
        $updateStmt->bindParam(':new_password', $hashedPassword);
        $updateStmt->bindParam(':id_user', $userId);
        
        if ($updateStmt->execute()) {
            echo json_encode(["message" => "Mot de passe changé avec succès."]);
        } else {
            echo json_encode(["error" => "Erreur lors de la mise à jour du mot de passe."]);
        }
    } else {
        echo json_encode(["error" => "L'ancien mot de passe est incorrect."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors du traitement de la requête : " . $e->getMessage()]);
}
?>