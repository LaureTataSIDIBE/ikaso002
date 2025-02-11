<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "dbconnect.php"; // Fichier de connexion à la base de données

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Lister les patients
    if ($action === 'list_patients' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $query = "SELECT id_patient, email, nom FROM patient JOIN user ON patient.id_user = user.id_user";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($patients);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la récupération des patients: " . $e->getMessage()]);
        }
    }

    // Lister les médecins
    elseif ($action === 'list_medecins' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $query = "SELECT id_user, email, nom, specialisation FROM medecin JOIN user ON medecin.id_user = user.id_user";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($medecins);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la récupération des médecins: " . $e->getMessage()]);
        }
    }

    // Ajouter un patient
    elseif ($action === 'add_patient' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->email, $data->nom)) {
            try {
                $query = "INSERT INTO user (email, nom) VALUES (:email, :nom)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':email', $data->email, PDO::PARAM_STR);
                $stmt->bindParam(':nom', $data->nom, PDO::PARAM_STR);
                $stmt->execute();
                $id_user = $pdo->lastInsertId();

                $query = "INSERT INTO patient (id_user) VALUES (:id_user)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(["success" => "Patient ajouté avec succès."]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de l'ajout : " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Paramètres manquants (email, nom)."]);
        }
    }

    // Ajouter un médecin
    elseif ($action === 'add_medecin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->email, $data->nom, $data->specialisation)) {
            try {
                $query = "INSERT INTO user (email, nom) VALUES (:email, :nom)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':email', $data->email, PDO::PARAM_STR);
                $stmt->bindParam(':nom', $data->nom, PDO::PARAM_STR);
                $stmt->execute();
                $id_user = $pdo->lastInsertId();

                $query = "INSERT INTO medecin (id_user, specialisation) VALUES (:id_user, :specialisation)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                $stmt->bindParam(':specialisation', $data->specialisation, PDO::PARAM_STR);
                $stmt->execute();

                echo json_encode(["success" => "Médecin ajouté avec succès."]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de l'ajout : " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Paramètres manquants (email, nom, specialisation)."]);
        }
    }

    // Modifier un patient
    elseif ($action === 'update_patient' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_patient, $data->email, $data->nom)) {
            try {
                $query = "UPDATE user SET email = :email, nom = :nom WHERE id_user = (SELECT id_user FROM patient WHERE id_patient = :id_patient)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':email', $data->email, PDO::PARAM_STR);
                $stmt->bindParam(':nom', $data->nom, PDO::PARAM_STR);
                $stmt->bindParam(':id_patient', $data->id_patient, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(["success" => "Patient modifié avec succès."]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de la modification : " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Paramètres manquants (id_patient, email, nom)."]);
        }
    }

    // Modifier un médecin
    elseif ($action === 'update_medecin' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_user, $data->email, $data->nom, $data->specialisation)) {
            try {
                $query = "UPDATE user SET email = :email, nom = :nom WHERE id_user = :id_user";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':email', $data->email, PDO::PARAM_STR);
                $stmt->bindParam(':nom', $data->nom, PDO::PARAM_STR);
                $stmt->bindParam(':id_user', $data->id_user, PDO::PARAM_INT);
                $stmt->execute();

                $query = "UPDATE medecin SET specialisation = :specialisation WHERE id_user = :id_user";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':specialisation', $data->specialisation, PDO::PARAM_STR);
                $stmt->bindParam(':id_user', $data->id_user, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(["success" => "Médecin modifié avec succès."]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de la modification : " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Paramètres manquants (id_user, email, nom, specialisation)."]);
        }
    }

    // Supprimer un patient
    elseif ($action === 'delete_patient' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_patient)) {
            try {
                $query = "DELETE FROM patient WHERE id_patient = :id_patient";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id_patient', $data->id_patient, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer aussi l'utilisateur lié
                $query = "DELETE FROM user WHERE id_user = (SELECT id_user FROM patient WHERE id_patient = :id_patient)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id_patient', $data->id_patient, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(["success" => "Patient supprimé avec succès."]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de la suppression : " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Paramètre 'id_patient' manquant."]);
        }
    }

    // Supprimer un médecin
    elseif ($action === 'delete_medecin' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_user)) {
            try {
                $query = "DELETE FROM medecin WHERE id_user = :id_user";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id_user', $data->id_user, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer aussi l'utilisateur lié
                $query = "DELETE FROM user WHERE id_user = :id_user";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id_user', $data->id_user, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(["success" => "Médecin supprimé avec succès."]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de la suppression : " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Paramètre 'id_user' manquant."]);
        }
    }

    // Gérer les erreurs d'action inconnue
    else {
        http_response_code(400);
        echo json_encode(["error" => "Action inconnue ou méthode non autorisée."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Paramètre 'action' manquant."]);
}
?>