<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

try {
    // Récupérer tous les utilisateurs de type "admin"
    $query = "SELECT * FROM user WHERE type_user = 'admin'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($admins as $admin) {
        // Vérifier si l'id_user existe déjà dans la table admin
        $checkQuery = "SELECT COUNT(*) FROM admin WHERE id_user = :id_user";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(['id_user' => $admin['id_user']]);
        $exists = $checkStmt->fetchColumn();
    
        if ($exists == 0) {
            // Insérer chaque admin dans la table admin sans id_admin
            $insertQuery = "INSERT INTO admin (id_user) VALUES (:id_user)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute(['id_user' => $admin['id_user']]);
        }
    }

    // Récupérer les administrateurs de la table admin
    $finalQuery = "SELECT a.id_admin, u.email, u.nom FROM admin a JOIN user u ON a.id_user = u.id_user";
    $finalStmt = $pdo->prepare($finalQuery);
    $finalStmt->execute();
    
    $finalAdmins = $finalStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'admins' => $finalAdmins,
        'message' => 'Admins insérés et récupérés avec succès.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne : ' . $e->getMessage()
    ]);
}
?>