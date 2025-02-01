<?php
$tableName = $_GET['tableName'];
$numSimulations = $_GET['numSimulations'] ?? 10; // Nombre de simulations (par défaut : 10)

$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Récupérer les noms des colonnes de la table
$query = "SHOW COLUMNS FROM $tableName";
$result = mysqli_query($conn, $query);
$columns = array();
while ($row = mysqli_fetch_assoc($result)) {
    $columns[] = $row['Field'];
}

$simulationResults = array();

for ($i = 0; $i < $numSimulations; $i++) {
    // Sélectionner des colonnes aléatoires pour les requêtes
    do {
        $randomColumn1 = mysqli_real_escape_string($conn, $columns[array_rand($columns)]);
        $randomColumn2 = mysqli_real_escape_string($conn, $columns[array_rand($columns)]);
    } while ($randomColumn1 === $randomColumn2);

    // Démarrer une transaction
    mysqli_begin_transaction($conn);

    // Temps d'exécution de la requête SELECT
    $startTime = microtime(true);
    $query = "SELECT * FROM $tableName";
    $result = mysqli_query($conn, $query);
    $endTime = microtime(true);
    $selectTime = ($endTime - $startTime) * 1000;
    $simulationResults['select'][] = $selectTime;

    // Temps d'exécution de la requête INSERT
    $startTime = microtime(true);
    $query = "INSERT INTO $tableName (`$randomColumn1`, `$randomColumn2`) VALUES ('value1', 'value2')";
    mysqli_query($conn, $query);
    $endTime = microtime(true);
    $insertTime = ($endTime - $startTime) * 1000;
    $simulationResults['insert'][] = $insertTime;

    // Temps d'exécution de la requête UPDATE
    $startTime = microtime(true);
    $query = "UPDATE $tableName SET `$randomColumn1` = 'new_value' WHERE id = 1";
    mysqli_query($conn, $query);
    $endTime = microtime(true);
    $updateTime = ($endTime - $startTime) * 1000;
    $simulationResults['update'][] = $updateTime;

    // Temps d'exécution de la requête DELETE
    $startTime = microtime(true);
    $query = "DELETE FROM $tableName WHERE id = 1";
    mysqli_query($conn, $query);
    $endTime = microtime(true);
    $deleteTime = ($endTime - $startTime) * 1000;
    $simulationResults['delete'][] = $deleteTime;

    // Temps d'exécution de la requête avec une condition
    $startTime = microtime(true);
    $query = "SELECT * FROM $tableName WHERE `$randomColumn1` = 'value'";
    $result = mysqli_query($conn, $query);
    $endTime = microtime(true);
    $conditionTime = ($endTime - $startTime) * 1000;
    $simulationResults['condition'][] = $conditionTime;
    
    // Annuler la transaction
    mysqli_rollback($conn);
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Résultats de la simulation</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="simulationChart"></canvas>

    <script>
        var simulationData = <?php echo json_encode($simulationResults); ?>;
        var labels = Array.from({length: <?php echo $numSimulations; ?>}, (_, i) => 'Simulation ' + (i + 1));

        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'SELECT',
                    data: simulationData.select,
                    borderColor: 'blue',
                    fill: false
                },
                {
                    label: 'INSERT',
                    data: simulationData.insert,
                    borderColor: 'green',
                    fill: false
                },
                {
                    label: 'UPDATE',
                    data: simulationData.update,
                    borderColor: 'orange',
                    fill: false
                },
                {
                    label: 'DELETE',
                    data: simulationData.delete,
                    borderColor: 'red',
                    fill: false
                },
                {
                    label: 'Condition',
                    data: simulationData.condition,
                    borderColor: 'purple',
                    fill: false
                }
            ]
        };

        var options = {
            responsive: true,
            title: {
                display: true,
                text: 'Résultats de la simulation'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Temps d\'exécution (ms)'
                    }
                }
            }
        };

        var ctx = document.getElementById('simulationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: data,
            options: options
        });
    </script>
</body>
</html>
