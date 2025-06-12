<?php
require_once 'config.php';

$tableName = $_GET['tableName'];
$numSimulations = $_GET['numSimulations'] ?? 10; // Nombre de simulations (par défaut : 10)

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Récupérer les noms des colonnes et leurs types
$query = "SHOW COLUMNS FROM `$tableName`";
$result = mysqli_query($conn, $query);
$columns = array();
$numericColumns = array();
$textColumns = array();

while ($row = mysqli_fetch_assoc($result)) {
    $columns[] = $row['Field'];
    $type = strtolower($row['Type']);
    
    if (strpos($type, 'decimal') !== false || strpos($type, 'int') !== false || strpos($type, 'float') !== false) {
        $numericColumns[] = $row['Field'];
    } else {
        $textColumns[] = $row['Field'];
    }
}

$simulationResults = array();

// Obtenir un échantillon de données réelles pour les tests
$sampleQuery = "SELECT * FROM `$tableName` LIMIT 5";
$sampleResult = mysqli_query($conn, $sampleQuery);
$sampleData = [];
while ($row = mysqli_fetch_assoc($sampleResult)) {
    $sampleData[] = $row;
}

for ($i = 0; $i < $numSimulations; $i++) {

    // Temps d'exécution de la requête SELECT complète
    $startTime = microtime(true);
    $query = "SELECT * FROM `$tableName`";
    $result = mysqli_query($conn, $query);
    $endTime = microtime(true);
    $selectTime = ($endTime - $startTime) * 1000;
    $simulationResults['select_all'][] = $selectTime;

    // Temps d'exécution de la requête SELECT avec LIMIT
    $startTime = microtime(true);
    $query = "SELECT * FROM `$tableName` LIMIT 100";
    $result = mysqli_query($conn, $query);
    $endTime = microtime(true);
    $selectLimitTime = ($endTime - $startTime) * 1000;
    $simulationResults['select_limit'][] = $selectLimitTime;

    // Temps d'exécution de la requête avec WHERE sur colonne numérique
    if (!empty($numericColumns)) {
        $randomNumCol = $numericColumns[array_rand($numericColumns)];
        $startTime = microtime(true);
        $query = "SELECT * FROM `$tableName` WHERE `$randomNumCol` > 5";
        $result = mysqli_query($conn, $query);
        $endTime = microtime(true);
        $whereNumTime = ($endTime - $startTime) * 1000;
        $simulationResults['where_numeric'][] = $whereNumTime;
    }

    // Temps d'exécution de la requête avec WHERE sur colonne texte
    if (!empty($textColumns)) {
        $randomTextCol = $textColumns[array_rand($textColumns)];
        $startTime = microtime(true);
        $query = "SELECT * FROM `$tableName` WHERE `$randomTextCol` LIKE '%a%'";
        $result = mysqli_query($conn, $query);
        $endTime = microtime(true);
        $whereTextTime = ($endTime - $startTime) * 1000;
        $simulationResults['where_text'][] = $whereTextTime;
    }

    // Temps d'exécution de la requête COUNT
    $startTime = microtime(true);
    $query = "SELECT COUNT(*) FROM `$tableName`";
    $result = mysqli_query($conn, $query);
    $endTime = microtime(true);
    $countTime = ($endTime - $startTime) * 1000;
    $simulationResults['count'][] = $countTime;

    // Temps d'exécution de la requête AVG sur colonne numérique
    if (!empty($numericColumns)) {
        $randomNumCol = $numericColumns[array_rand($numericColumns)];
        $startTime = microtime(true);
        $query = "SELECT AVG(`$randomNumCol`) FROM `$tableName`";
        $result = mysqli_query($conn, $query);
        $endTime = microtime(true);
        $avgTime = ($endTime - $startTime) * 1000;
        $simulationResults['avg'][] = $avgTime;
    }
}

closeConnection($conn);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Tests de Performance - <?php echo htmlspecialchars($tableName); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .chart-container { width: 100%; height: 400px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Tests de Performance</h1>
    <div class="info">
        <strong>Table testée:</strong> <?php echo htmlspecialchars($tableName); ?><br>
        <strong>Nombre de simulations:</strong> <?php echo $numSimulations; ?><br>
        <strong>Colonnes numériques:</strong> <?php echo implode(', ', $numericColumns); ?><br>
        <strong>Colonnes texte:</strong> <?php echo implode(', ', $textColumns); ?>
    </div>
    
    <div class="chart-container">
        <canvas id="simulationChart"></canvas>
    </div>

    <script>
        var simulationData = <?php echo json_encode($simulationResults); ?>;
        var labels = Array.from({length: <?php echo $numSimulations; ?>}, (_, i) => 'Simulation ' + (i + 1));

        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'SELECT ALL',
                    data: simulationData.select_all,
                    borderColor: 'blue',
                    fill: false
                },
                {
                    label: 'SELECT LIMIT 100',
                    data: simulationData.select_limit,
                    borderColor: 'lightblue',
                    fill: false
                },
                {
                    label: 'WHERE Numérique',
                    data: simulationData.where_numeric,
                    borderColor: 'green',
                    fill: false
                },
                {
                    label: 'WHERE Texte',
                    data: simulationData.where_text,
                    borderColor: 'orange',
                    fill: false
                },
                {
                    label: 'COUNT',
                    data: simulationData.count,
                    borderColor: 'red',
                    fill: false
                },
                {
                    label: 'AVG',
                    data: simulationData.avg,
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
