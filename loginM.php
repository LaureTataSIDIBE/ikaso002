<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('display_errors', 1);
error_reporting(E_ALL);

include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['mot_de_passe'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Email ou mot de passe manquant.'
    ]);
    exit();
}

$email = trim($data['email']);
$motdepasse = trim($data['mot_de_passe']);

try {
    $query = "SELECT * FROM user WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($motdepasse, $user['mot_de_passe'])) {
            $type_user = $user['type_user'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['statut'] = $type_user;

            $response = [
                'success' => true,
                'message' => 'Connexion réussie.',
                'type_user' => $type_user,
                'nom' => $user['nom'],
                'email' => $user['email'],
                'id_user' => $user['id_user']
            ];

            // Récupérer id_patient ou id_medecin en fonction du type d'utilisateur
            $iduser = $user['id_user'];
            if ($type_user == "patient") {
                $query = "SELECT * FROM patient WHERE id_user = :iduser";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                $stmt->execute();
                $patient = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($patient) {
                    $response['id_patient'] = $patient['id_patient'];
                }
            } elseif ($type_user == "medecin") {
                $query = "SELECT * FROM medecin WHERE id_user = :iduser";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                $stmt->execute();
                $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($medecin) {
                    $response['id_medecin'] = $medecin['id_medecin'];
                }
            }

            echo json_encode($response);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Mot de passe incorrect.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur non trouvé avec cet email.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne : ' . $e->getMessage()
    ]);
}
?>