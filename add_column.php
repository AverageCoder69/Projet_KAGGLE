<?php
$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

if(!$conn){
    echo "Connection failed: ".mysqli_connect_error();
}

if (isset($_POST['tableName']) && isset($_POST['columnName'])) {
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);
    $columnName = mysqli_real_escape_string($conn, $_POST['columnName']);

    // Vérifier si la colonne existe déjà
    $checkSql = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "Une colonne avec le nom $columnName existe déjà dans la table $tableName.";
    } else {
        $sql = "ALTER TABLE $tableName ADD $columnName VARCHAR(255)";

        if (mysqli_query($conn, $sql)) {
            $result = mysqli_query($conn, "SHOW COLUMNS FROM $tableName");
            $columns = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $columns[] = $row['Field'];
            }
            echo json_encode($columns);
        } else {
            echo "Erreur: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>