<?php
include 'config.php'; // Fichier pour la connexion à la base de données

// Supprimer un produit si une requête POST est reçue
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Préparer la requête de suppression
    $requete = "DELETE FROM categories WHERE id = ?";
    $stmt = $cnx->prepare($requete);

    if (!$stmt) {
        die("Erreur de préparation de la requête: " . $cnx->error);
    }

    // Lier les paramètres et exécuter la requête
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "Produit supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du produit: " . $stmt->error;
    }

    $stmt->close();
}

// Afficher tous les produits
$query = "SELECT * FROM categories";
$result = $cnx->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
</head>
<body>
    <h2>Liste des Produits</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom du Produit</th>
            <th>Nom de la Catégorie</th>
            <th>Prix</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nomp'] . "</td>";
                echo "<td>" . $row['nom'] . "</td>";
                echo "<td>" . $row['prix'] . "</td>";
                echo "<td><img src='" . $row['image'] . "' alt='Image' width='100'></td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                            <input type='submit' value='Supprimer'>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Aucun produit trouvé.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$cnx->close();
?>