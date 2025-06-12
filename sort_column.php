<?php
require_once 'config.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$tableName = $_POST['tableName'];
$columnName = $_POST['columnName'];
$sortOrder = $_POST['sortOrder'];

$query = "SELECT * FROM $tableName ORDER BY `$columnName` $sortOrder";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<table id='myTable'><thead><tr>";
    // Afficher les noms des colonnes
    $columnNames = mysqli_fetch_fields($result);
    foreach ($columnNames as $column) {
        echo "<th class='sortable' onclick='sortColumn(\"" . $column->name . "\")'>" . $column->name . "<i class='fas fa-sort'></i></th>";
    }    
    echo "</tr></thead><tbody>";
    
    // Afficher les données
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td id='row_" . $row['ID'] . "_" . $key . "'>" . $value . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "0 results";
}

closeConnection($conn);
?>
