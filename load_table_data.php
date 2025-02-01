<?php
$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

if(!$conn){
    echo "Connection failed: ".mysqli_connect_error();
}

if (isset($_POST['tableName'])) {
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);
    
    // Vérifier si la table existe dans la base de données
    $tableExistsQuery = "SHOW TABLES LIKE '$tableName'";
    $tableExistsResult = mysqli_query($conn, $tableExistsQuery);
    
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

            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($columns as $column) {
                    $cellId = "cell_".$row['ID']."_".$column;
                    echo "<td id='$cellId'>" . $row[$column] . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "0 results";
        }
    } else {
        echo "Veuillez choisir une table dans la liste déroulante";
    }
}

mysqli_close($conn);
?>
