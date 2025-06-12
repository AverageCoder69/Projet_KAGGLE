<?php
require_once 'config.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
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

closeConnection($conn);

?>