<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Connexion à la base de données
include "dbconnect.php"; // Assurez-vous que ce fichier initialise un objet $pdo

try {
    // Récupérer les rendez-vous pour un médecin spécifique
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Vérifier si un ID médecin est fourni
        $medecinId = isset($_GET['id_medecin']) ? intval($_GET['id_medecin']) : null;

        if ($medecinId) {
            $query = "SELECT * FROM `user`
                      JOIN `patient` ON user.id_user = patient.id_user
                      JOIN `rendezvous` ON patient.id_patient = rendezvous.id_patient
                      WHERE rendezvous.id_medecin = :id_medecin";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_medecin', $medecinId, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                echo json_encode(['rendezvous' => $results]);
            } else {
                echo json_encode(["message" => "Aucun rendez-vous trouvé pour ce médecin."]);
            }
        } else {
            echo json_encode(["error" => "ID médecin non fourni."]);
        }
    }

    // Accepter, annuler ou reporter un rendez-vous
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['appointment_id']) || !isset($data['action'])) {
            echo json_encode(["error" => "ID de rendez-vous ou action manquante."]);
            exit;
        }

        $appointmentId = $data['appointment_id'];
        $action = $data['action']; // 'accept', 'cancel', 'reschedule'

        switch ($action) {
            case 'accept':
                $updateQuery = "UPDATE rendezvous SET statut = 'accepté' WHERE id_rendezvous = :appointment_id";
                break;
            case 'cancel':
                $updateQuery = "UPDATE rendezvous SET statut = 'annulé' WHERE id_rendezvous = :appointment_id";
                break;
            case 'reschedule':
                if (!isset($data['new_date']) || !isset($data['new_time'])) {
                    echo json_encode(["error" => "Date ou heure de report manquante."]);
                    exit;
                }
                $newDate = $data['new_date'];
                $newTime = $data['new_time'];
                $updateQuery = "UPDATE rendezvous SET mydate = :new_date, heure = :new_time, statut = 'reporté' WHERE id_rendezvous = :appointment_id";
                break;
            default:
                echo json_encode(["error" => "Action non reconnue."]);
                exit;
        }

        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);

        if ($action === 'reschedule') {
            $stmt->bindParam(':new_date', $newDate);
            $stmt->bindParam(':new_time', $newTime);
        }

        if ($stmt->execute()) {
            echo json_encode(["message" => "Rendez-vous $action avec succès."]);
        } else {
            echo json_encode(["error" => "Erreur lors de la mise à jour du rendez-vous."]);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de la récupération des rendez-vous : " . $e->getMessage()]);
}
?>