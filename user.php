<?php
// user.php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Inclure la connexion à la base de données
include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

// Récupérer les données envoyées par Flutter
$data = $_POST;

if (!isset($data['email']) || !isset($data['motdepasse']) || !isset($data['nom']) || !isset($data['prenom'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont obligatoires.']);
    exit;
}

$email = $pdo->quote($data['email']);
$motdepasse = password_hash($data['motdepasse'], PASSWORD_BCRYPT);
$nom = $pdo->quote($data['nom']);
$prenom = $pdo->quote($data['prenom']);

// Vérifier si l'utilisateur existe déjà
$sql_check = "SELECT * FROM user WHERE email = $email";
$result = $pdo->query($sql_check);

if ($result->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cet utilisateur existe déjà.']);
} else {
    // Insérer l'utilisateur dans la base de données
    $sql_insert = "INSERT INTO user (nom, prenom, email, mot_de_passe) VALUES ($nom, $prenom, $email, '$motdepasse')";

    if ($pdo->exec($sql_insert)) {
        echo json_encode(['status' => 'success', 'message' => 'Inscription réussie.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'insertion dans la base de données.']);
    }
}

// Fermer la connexion (facultatif, car PDO se ferme automatiquement)
$pdo = null;
?>