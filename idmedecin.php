<?php
session_start();
// Définir l'en-tête pour la réponse JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include('dbconnect.php'); // Assurez-vous que ce fichier initialise un objet PDO appelé $pdo

$data = json_decode(file_get_contents('php://input'), true);
$iduser = $_GET['id_user']; // Ajout du point-virgule ici

// Requête SQL pour vérifier si l'utilisateur existe
$query = "SELECT * FROM `medecin` WHERE id_user = :iduser"; 
$stmt = $pdo->prepare($query);
$stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT); // Utilisation de bindParam

// Exécuter la requête avec des paramètres sécurisés
$stmt->execute();

// Récupérer les résultats
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Correction ici

// Vous pouvez maintenant retourner les résultats ou effectuer d'autres actions
echo json_encode($user); // Par exemple, renvoyer les données de l'utilisateur
?>