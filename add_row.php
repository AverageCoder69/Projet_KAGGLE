<?php
// Connectez-vous à votre base de données
$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

// Récupérez le nom de la table de la requête AJAX
$tableName = $_POST['tableName'];

// Récupérez les noms de colonnes de la table
$columnQuery = "SHOW COLUMNS FROM $tableName";
$columnResult = mysqli_query($conn, $columnQuery);
$columns = array();
while ($columnRow = mysqli_fetch_assoc($columnResult)) {
    $columns[] = '`' . $columnRow['Field'] . '`';  // Entourer les noms de colonnes avec des accents graves
}

// Construisez la requête SQL pour ajouter une nouvelle ligne vide
$sql = "INSERT INTO `$tableName` (";  // Entourer le nom de la table avec des accents graves
$sql .= implode(", ", array_diff($columns, ['`ID`']));  // Exclure la colonne ID
$sql .= ") VALUES (";
$sql .= "'" . implode("', '", array_fill(0, count($columns) - 1, '')) . "'";
$sql .= ")";

// Exécutez la requête SQL
if (mysqli_query($conn, $sql)) {
    echo "Une nouvelle ligne vide a été ajoutée avec succès.";
} else {
    echo "Erreur lors de l'ajout d'une nouvelle ligne : " . mysqli_error($conn);
}

mysqli_close($conn);
?>