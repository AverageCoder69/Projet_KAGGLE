<?php

$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

// Vérifier la connexion
if (!$conn) {
    echo "Connection failed: ".mysqli_connect_error();
}

if (isset($_POST['tableName'], $_POST['columnName'], $_POST['statType'])) {
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);
    $columnName = mysqli_real_escape_string($conn, $_POST['columnName']);
    $statType = $_POST['statType'];

    if ($statType == 'Moyenne') {
        $sql = "SELECT AVG(`$columnName`) AS avg FROM `$tableName`";
    } elseif ($statType == 'Médiane') {
        $countSql = "SELECT COUNT(*) AS count FROM `$tableName`";
        $countResult = mysqli_query($conn, $countSql);
        $countRow = mysqli_fetch_assoc($countResult);
        $count = $countRow['count'];

        if ($count % 2 == 0) {
            $offset = ($count / 2) - 1;
            $sql = "SELECT AVG(`$columnName`) AS median FROM (
                        SELECT `$columnName` FROM `$tableName` ORDER BY `$columnName` LIMIT 2 OFFSET $offset
                    ) AS t";
        } else {
            $offset = floor($count / 2);
            $sql = "SELECT `$columnName` AS median FROM `$tableName` ORDER BY `$columnName` LIMIT 1 OFFSET $offset";
        }
    } elseif ($statType == 'Écart-type') {
        $sql = "SELECT STDDEV(`$columnName`) AS stddev FROM `$tableName`";
    } elseif ($statType == 'Maxima') {
        $sql = "SELECT MAX(`$columnName`) AS max FROM `$tableName`";
    } elseif ($statType == 'Minima') {
        $sql = "SELECT MIN(`$columnName`) AS min FROM `$tableName`";
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($statType == 'Moyenne') {
            echo $row['avg'];
        } elseif ($statType == 'Médiane') {
            echo $row['median'];
        } elseif ($statType == 'Écart-type') {
            echo $row['stddev'];
        } elseif ($statType == 'Maxima') {
            echo $row['max'];
        } elseif ($statType == 'Minima') {
            echo $row['min'];
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);

?>