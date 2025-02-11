<?php
include 'config.php'; // Fichier pour la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifiez si un fichier a été téléchargé
    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];

        // Vérifiez si le fichier a été téléchargé sans erreur
        if ($image['error'] == 0) {
            $nom_categorie = $_POST['nom'];
            $nomp = $_POST['nomp'];
            $prix = $_POST['prix']; // Récupère le prix

            // Vérifiez le type de fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $allowed_types)) {
                echo "Type d'image non valide. Seules les images JPG, PNG et GIF sont autorisées.";
                exit;
            }

            // Définir le chemin de destination
            $upload_dir = 'imagiii/'; // Assurez-vous que ce dossier existe et a les bonnes permissions
            $image_name = basename($image['name']);
            $target_file = $upload_dir . uniqid() . '-' . $image_name; // Pour éviter les conflits de noms

            // Déplacer le fichier téléchargé vers le dossier de destination
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                // Préparer la requête d'insertion
                $requete = "INSERT INTO categories (nomp, nom, image, prix) VALUES (?, ?, ?, ?)";
                $stmt = $cnx->prepare($requete);
                
                if (!$stmt) {
                    die("Erreur de préparation de la requête: " . $cnx->error);
                }

                // Lier les paramètres et exécuter la requête
                $stmt->bind_param("sssd", $nomp, $nom_categorie, $target_file, $prix);
                if ($stmt->execute()) {
                    echo "Catégorie ajoutée avec succès.";
                } else {
                    echo "Erreur lors de l'ajout de la catégorie: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Erreur lors du déplacement de l'image. Vérifiez les permissions du dossier.";
            }
        } else {
            echo "Erreur lors du téléchargement de l'image: " . $image['error'];
        }
    } else {
        echo "Aucune image téléchargée.";
    }
}

$cnx->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie</title>
</head>
<body>
    <h2>Ajouter une Catégorie</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="nomp">Nom du produit:</label>
        <input type="text" name="nomp" required><br>
        <label for="nom">Nom de la catégorie:</label>
        <input type="text" name="nom" required><br>
        <label for="prix">Prix:</label>
        <input type="number" name="prix" step="0.01" required><br> <!-- Champ pour le prix -->
        
        <label for="image">Télécharger une image:</label>
        <input type="file" name="image" accept="image/*" required><br>
        
        <input type="submit" value="Ajouter la Catégorie">
    </form>
</body>
</html>