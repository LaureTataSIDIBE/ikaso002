<?php
// Configuration de la connexion à la base de données
$host = 'localhost'; // Nom de l'hôte (ex: localhost)
$dbname = 'ikaso001'; // Nom de la base de données
$username = 'root'; // Nom d'utilisateur MySQL
$password = ''; // Mot de passe MySQL

try {
    // Création de l'objet PDO pour la connexion
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
