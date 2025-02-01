<?php

$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

// Vérifier la connexion
if(!$conn){
    die("Connection failed: ".mysqli_connect_error());
}

if (isset($_POST['tableName'])) {
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);
    $sql = "SHOW COLUMNS FROM " . $tableName;
    $result = mysqli_query($conn, $sql);

    echo '<option selected disabled>Sélectionnez une colonne</option>';
    while($column = mysqli_fetch_array($result)) {
        echo '<option value="' . $column['Field'] . '">' . $column['Field'] . '</option>';
    }
}

mysqli_close($conn);

?>