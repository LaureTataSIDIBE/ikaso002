<?php
require_once 'dbconnect.php';

// Ajouter une consultation
function addConsultation($patient_id, $medecin_id, $date_consultation, $diagnostic = null, $traitement = null, $commentaire = null) {
    global $pdo;

    $query = $pdo->prepare("INSERT INTO Consultation (patient_id, medecin_id, date_consultation, diagnostic, traitement, commentaire) 
                            VALUES (:patient_id, :medecin_id, :date_consultation, :diagnostic, :traitement, :commentaire)");
    $query->execute([
        'patient_id' => $patient_id,
        'medecin_id' => $medecin_id,
        'date_consultation' => $date_consultation,
        'diagnostic' => $diagnostic,
        'traitement' => $traitement,
        'commentaire' => $commentaire
    ]);

    return $pdo->lastInsertId();
}

// Récupérer toutes les consultations
function getAllConsultations() {
    global $pdo;

    $query = $pdo->query("SELECT consultation.*, 
                          patient.nom AS patient_nom, patient.prenom AS patient_prenom, 
                          medecin.nom AS medecin_nom, medecin.prenom AS medecin_prenom 
                          FROM Consultation
                          LEFT JOIN Patient ON consultation.patient_id = patient.id_patient
                          LEFT JOIN Medecin ON consultation.medecin_id = medecin.id_medecin");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer une consultation par ID
function getConsultationById($id) {
    global $pdo;

    $query = $pdo->prepare("SELECT consultation.*, 
                            patient.nom AS patient_nom, patient.prenom AS patient_prenom, 
                            medecin.nom AS medecin_nom, medecin.prenom AS medecin_prenom 
                            FROM Consultation
                            LEFT JOIN Patient ON consultation.patient_id = patient.id_patient
                            LEFT JOIN Medecin ON consultation.medecin_id = medecin.id_medecin
                            WHERE consultation.id_consultation = :id");
    $query->execute(['id' => $id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Supprimer une consultation
function deleteConsultation($id) {
    global $pdo;

    $query = $pdo->prepare("DELETE FROM Consultation WHERE id_consultation = :id");
    $query->execute(['id' => $id]);
}
?>
