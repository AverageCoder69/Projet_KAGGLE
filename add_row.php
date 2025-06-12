<?php
require_once 'config.php';

// Connectez-vous à votre base de données
try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Récupérez le nom de la table de la requête AJAX
$tableName = $_POST['tableName'];

// Récupérez les noms et types de colonnes de la table
$columnQuery = "SHOW COLUMNS FROM `$tableName`";
$columnResult = mysqli_query($conn, $columnQuery);
$columns = array();
$values = array();

while ($columnRow = mysqli_fetch_assoc($columnResult)) {
    $columnName = $columnRow['Field'];
    $columnType = strtolower($columnRow['Type']);
    
    // Traiter la colonne ID spécialement
    if ($columnName === 'ID') {
        // Obtenir le prochain ID disponible
        $maxIdQuery = "SELECT MAX(ID) as max_id FROM `$tableName`";
        $maxIdResult = mysqli_query($conn, $maxIdQuery);
        $maxIdRow = mysqli_fetch_assoc($maxIdResult);
        $nextId = ($maxIdRow['max_id'] ?? 0) + 1;
        
        $columns[] = '`ID`';
        $values[] = $nextId;
        continue;
    }
    
    $columns[] = '`' . $columnName . '`';
    
    // Déterminer la valeur par défaut selon le type
    if (strpos($columnType, 'decimal') !== false || 
        strpos($columnType, 'int') !== false || 
        strpos($columnType, 'float') !== false) {
        // Pour les colonnes numériques, utiliser NULL
        $values[] = 'NULL';
    } else {
        // Pour les colonnes texte, utiliser chaîne vide
        $values[] = "''";
    }
}

// Construisez la requête SQL pour ajouter une nouvelle ligne avec valeurs par défaut
$sql = "INSERT INTO `$tableName` (";
$sql .= implode(", ", $columns);
$sql .= ") VALUES (";
$sql .= implode(", ", $values);
$sql .= ")";

// Exécutez la requête SQL
if (mysqli_query($conn, $sql)) {
    echo "Une nouvelle ligne vide a été ajoutée avec succès.";
} else {
    echo "Erreur lors de l'ajout d'une nouvelle ligne : " . mysqli_error($conn);
}

closeConnection($conn);
?>