<?php
include 'config.php'; // Fichier pour la connexion à la base de données

// Vérifiez si un ID est passé en paramètre
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les détails du produit
    $query = "SELECT * FROM categories WHERE id = ?";
    $stmt = $cnx->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Produit non trouvé.";
        exit;
    }
} else {
    echo "Aucun ID de produit spécifié.";
    exit;
}

// Mettre à jour le produit si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_categorie = $_POST['nom'];
    $nomp = $_POST['nomp'];
    $prix = $_POST['prix'];
    $image_path = $_POST['image'];

    // Préparer la requête de mise à jour
    $requete = "UPDATE categories SET nomp = ?, nom = ?, prix = ?, image = ? WHERE id = ?";
    $stmt = $cnx->prepare($requete);

    if (!$stmt) {
        die("Erreur de préparation de la requête: " . $cnx->error);
    }

    // Lier les paramètres et exécuter la requête
    $stmt->bind_param("ssdsi", $nomp, $nom_categorie, $prix, $image_path, $id);
    if ($stmt->execute()) {
        echo "Produit mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du produit: " . $stmt->error;
    }

    $stmt->close();
}

$cnx->close();
?>

