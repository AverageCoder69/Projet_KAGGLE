<?php
require_once 'config.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Récupérez le nom de la table à partir de la requête GET et échappez-le pour prévenir l'injection SQL
if(isset($_GET['tableName'])) {
    $tableName = mysqli_real_escape_string($conn, $_GET['tableName']);
} else {
    die("No table name provided.");
}

// Sélectionnez toutes les lignes de la table
$query = "SELECT * FROM {$tableName} LIMIT 18446744073709551615 OFFSET 1";
$result = mysqli_query($conn, $query);

// Ouvrez le flux de sortie PHP
$fp = fopen('php://output', 'w');

// Envoyez les en-têtes corrects au navigateur
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $tableName . '.csv"');

// Récupérez les noms de colonnes et écrivez-les dans le fichier CSV
$columnNames = array();
if (!empty($result)) {
    $firstRow = $result->fetch_assoc();
    foreach ($firstRow as $key => $value) {
        $columnNames[] = $key;
    }
    fputcsv($fp, $columnNames);
    // Écrivez la première ligne de données
    fputcsv($fp, $firstRow);
}

// Écrivez les lignes restantes dans le fichier CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($fp, $row);
}

// Fermez le flux de sortie
fclose($fp);

?>
