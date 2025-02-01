<?php

$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

// Vérifiez la connexion
if (!$conn) {
    die("Connection failed: " . mysqli_error($conn));
}

if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
    // Récupérez le nom de la table spécifié par l'utilisateur
    $new_table = $_POST['tableName'];

    // Vérifiez si le nom de la table est vide
    if (empty($new_table)) {
        echo "Veuillez spécifier un nom de table.";
        exit();
    }

    // Ouvrez le fichier CSV
    $file = fopen($_FILES['fileToUpload']['tmp_name'], 'r');

    // Obtenez les en-têtes du fichier
    $headers = fgetcsv($file);

    // Mettez des guillemets autour des noms de colonnes
    $headers = array_map(function($header) {
        return "`$header`";
    }, $headers);

    // Vérifiez si le premier en-tête est vide et remplacez-le par 'ID'
    if (empty($headers[0])) {
        $headers[0] = "`ID`";
    }

    // Vous devez définir les types de données pour chaque colonne.
    $columns = implode(", ", array_map(function($header) {
        return "$header TEXT";
    }, $headers));

    // Exécutez la requête SQL pour créer la table
    $sql = "CREATE TABLE IF NOT EXISTS `$new_table` ($columns)";
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Failed to create table: " . mysqli_error($conn));
    }

    // Importez chaque ligne du fichier dans la base de données
    while (($row = fgetcsv($file)) !== FALSE) {
        // Échappez les valeurs
        $row = array_map(function($cell) use ($conn) {
            return mysqli_real_escape_string($conn, $cell);
        }, $row);

        $sql = "INSERT INTO `$new_table` (" . implode(", ", $headers) . ") VALUES ('" . implode("', '", $row) . "')";

        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Failed to import row: " . mysqli_error($conn));
        }
    }

    // Fermez le fichier CSV
    fclose($file);

    echo "File imported successfully.";
} else {
    echo "Failed to import file.";
}

mysqli_close($conn);
?>