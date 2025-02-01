<?php
$conn = mysqli_connect("localhost", "Test", "Test1", "isfa");

if (!$conn) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

$boxplotGraphJson = json_encode(array());
$histogramGraphJson = json_encode(array());

if (isset($_GET['tableName']) && isset($_GET['columnName'])) {
    $tableName = mysqli_real_escape_string($conn, $_GET['tableName']);
    $columnName = mysqli_real_escape_string($conn, $_GET['columnName']);

    // Récupérer les données de la colonne sélectionnée
    $sql = "SELECT `$columnName` FROM `$tableName`";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $value = $row[$columnName];
        if (is_numeric($value)) {
            $data[] = floatval($value);
        }
    }

    // Vérifier si des données numériques ont été trouvées
    if (count($data) > 0) {
        // Générer le graphique de boxplot
        $boxplotData = array(
            array(
                "type" => "box",
                "y" => $data,
                "name" => $columnName,
                "boxpoints" => "all",
                "jitter" => 0.3,
                "pointpos" => -1.8
            )
        );
        $boxplotLayout = array(
            "title" => "Boxplot de $columnName",
            "yaxis" => array("title" => $columnName)
        );
        $boxplotGraphJson = json_encode(array("data" => $boxplotData, "layout" => $boxplotLayout));

        // Générer l'histogramme
        $histogramData = array(
            array(
                "type" => "histogram",
                "x" => $data,
                "name" => $columnName,
                "nbinsx" => 30,
                "opacity" => 0.7
            )
        );
        $histogramLayout = array(
            "title" => "Histogramme de $columnName",
            "xaxis" => array("title" => $columnName),
            "yaxis" => array("title" => "Fréquence")
        );
        $histogramGraphJson = json_encode(array("data" => $histogramData, "layout" => $histogramLayout));
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
    <div id="boxplot" style="width: 100%; height: 600px;"></div>
    <div id="histogram" style="width: 100%; height: 600px;"></div>

    <script>
        var boxplotGraphJson = <?php echo $boxplotGraphJson; ?>;
        var histogramGraphJson = <?php echo $histogramGraphJson; ?>;

        Plotly.newPlot('boxplot', boxplotGraphJson.data, boxplotGraphJson.layout);
        Plotly.newPlot('histogram', histogramGraphJson.data, histogramGraphJson.layout);
    </script>
</body>
</html>
