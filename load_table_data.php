<?php
require_once 'config.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if (isset($_POST['tableName'])) {
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);
    
    // Vérifier si c'est le message par défaut du dropdown
    if (strpos($tableName, 'Choisissez') !== false) {
        echo "<p>Veuillez choisir une table dans la liste en haut à gauche s'il vous plaît.</p>";
        closeConnection($conn);
        exit;
    }
    
    // Debug: Afficher le nom de la table reçue
    error_log("Table demandée: " . $tableName);
    
    // Vérifier si la table existe dans la base de données
    $tableExistsQuery = "SHOW TABLES LIKE '$tableName'";
    $tableExistsResult = mysqli_query($conn, $tableExistsQuery);
    
    error_log("Nombre de tables trouvées: " . mysqli_num_rows($tableExistsResult));
    
    if (mysqli_num_rows($tableExistsResult) > 0) {
        // Récupérer les noms de colonnes
        $columnQuery = "SHOW COLUMNS FROM $tableName";
        $columnResult = mysqli_query($conn, $columnQuery);
        $columns = array();
        while ($columnRow = mysqli_fetch_assoc($columnResult)) {
            $columns[] = $columnRow['Field'];
        }
        
        // Récupérer les données de la table
        $sql = "SELECT * FROM $tableName";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table id='myTable'><thead><tr>";
            // Afficher les en-têtes de colonne avec les classes CSS appropriées
            foreach ($columns as $column) {
                echo "<th class='sortable' onclick='sortColumn(\"$column\")'><span>$column</span><i class='fas fa-sort'></i></th>";
            }
            echo "</tr></thead><tbody>";

            $rowIndex = 0;
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($columns as $column) {
                    // Utiliser l'ID de la ligne si disponible, sinon utiliser l'index de ligne
                    $rowId = isset($row['ID']) ? $row['ID'] : $rowIndex;
                    // Utiliser || comme séparateur pour éviter les conflits avec _ dans les noms de colonnes
                    $cellId = "cell||".$rowId."||".$column;
                    echo "<td id='$cellId'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                }
                echo "</tr>";
                $rowIndex++;
            }
            echo "</tbody></table>";
        } else {
            echo "0 results";
            error_log("Aucun résultat trouvé pour la table: " . $tableName);
        }
    } else {
        echo "Table '$tableName' n'existe pas dans la base de données";
        error_log("Table n'existe pas: " . $tableName);
    }
} else {
    echo "Aucun nom de table reçu";
    error_log("Aucun tableName dans POST");
}

closeConnection($conn);
?>
