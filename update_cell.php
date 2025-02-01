<?php
$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

if (!$conn) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

if (isset($_POST['rowId']) && isset($_POST['newValue']) && isset($_POST['columnName']) && isset($_POST['tableName'])) {
    $rowId = mysqli_real_escape_string($conn, $_POST['rowId']);
    $newValue = mysqli_real_escape_string($conn, $_POST['newValue']);
    $columnName = mysqli_real_escape_string($conn, $_POST['columnName']);
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);

    // Vérifier si la colonne existe dans la table
    $checkColumnQuery = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";
    $checkColumnResult = mysqli_query($conn, $checkColumnQuery);

    if (mysqli_num_rows($checkColumnResult) > 0) {
        // La colonne existe, exécuter la requête de mise à jour
        $sql = "UPDATE $tableName SET $columnName = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $newValue, $rowId);

        if (mysqli_stmt_execute($stmt)) {
            echo "La cellule a été mise à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour de la cellule: " . mysqli_error($conn);
        }
    } else {
        echo "La colonne '$columnName' n'existe pas dans la table '$tableName'.";
    }
}

mysqli_close($conn);
?>
