
<?php
require_once 'config.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Récupérer seulement les tables uascores
$result = mysqli_query($conn, "SHOW TABLES");
$tables = [];

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_row($result)) {
        $tableName = $row[0];
        // Filtrer pour ne garder que les tables uascores
        if (strpos($tableName, 'uascores') === 0) {
            $tables[] = $tableName;
        }
    }
}

closeConnection($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- Un select pour choisir la table -->
    <select id="tableDropdown" onchange="loadTableData()">
        <option selected disabled>Choisissez une table</option>
        <?php foreach ($tables as $tableName): ?>
            <option value="<?php echo $tableName; ?>"><?php echo $tableName; ?></option>
        <?php endforeach; ?>
    </select>
    <!-- Un div pour afficher les données -->

    <h1 class="title" style='text-align: center;'>Bienvenue sur le site pour tester ma table</h1>

    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <button class="button_plus" onclick="zoomIn()">Zoom +</button>
        <button class="button_moins" onclick="zoomOut()">Zoom -</button>
        <button onclick="window.location.href='export.php?tableName='+document.getElementById('tableDropdown').value">Exporter en CSV</button>
        <select id="columnName">
            <option selected disabled>Sélectionnez une colonne</option>
        </select>
        <select id="statsDropdown" onchange="showStatsForColumn()">
            <option selected disabled>Afficher les statistiques</option>
            <option>Moyenne</option>
            <option>Médiane</option>
            <option>Écart-type</option>
            <option>Maxima</option>
            <option>Minima</option>
        </select>
        <button onclick="openGraphPage()">Afficher les graphiques pour une colonne</button>
<div id="graphContainer"></div>
    </div>

    <form id="importForm" action="import.php" method="post" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="hidden" name="tableName" id="tableName">
    <input type="submit" value="Importer" name="submit" onclick="promptTableName(event)">
</form>
<form action="graphsPerformance.php" method="get">
    <input type="hidden" name="tableName" value="<?php echo $tableName; ?>">
    <label for="numSimulations">Nombre de simulations (pour évaluer la performance d'une table) :</label>
    <input type="number" name="numSimulations" id="numSimulations" value="10" min="1">
    <button type="submit">Lancer la simulation</button>
</form>


    <button onclick="addColumn()">Ajouter une colonne</button>
    <button onclick="addRow()">Ajouter une ligne</button>
    <button id="saveButton">Enregistrer modifications</button>
    <p id="result"></p>
    <div id="message"></div>

    <div id="tableData"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            // Vérifier si une table est sélectionnée au chargement de la page
            var selectedTable = document.getElementById("tableDropdown").value;
            if (selectedTable) {
                loadTableData();
            } else {
                $('#tableData').html('<p>Veuillez choisir une table dans la liste en haut à gauche s\'il vous plaît.</p>');
            }
        });

        function loadTableData() {
    var tableName = document.getElementById("tableDropdown").value;
    if (tableName) {
        $.post('load_table_data.php', {tableName: tableName}, function(response) {
            $('#tableData').html(response);
            // Ajouter l'attribut contentEditable et lier l'événement blur et keypress
            $('#tableData td').attr('contenteditable', 'true').blur(updateCell).keypress(function(event) {
                if (event.which == 13) {
                    event.preventDefault();
                    updateCell.call(this);
                }
            });
            // Pour obtenir également les noms des colonnes
            $.post('get_column_names.php', {tableName: tableName}, function(response) {
                $('#columnName').html(response);
            });
            // Ajouter l'attribut onclick aux en-têtes de colonne
            $('#tableData th').each(function() {
                var columnName = $(this).text();
                $(this).attr('onclick', 'sortColumn("' + columnName + '")');
            });
        });
    } else {
        $('#tableData').html('<p>Veuillez choisir une table dans la liste en haut à gauche s\'il vous plaît.</p>');
    }
}

        function updateCell() {
    var cellId = $(this).attr('id');
    var newValue = $(this).text();
    var parts = cellId.split('||');
    var rowId = parts[1];
    var columnName = parts[2];
    var tableName = document.getElementById("tableDropdown").value;
    
    $.post('update_cell.php', {rowId: rowId, newValue: newValue, columnName: columnName, tableName: tableName}, function(response) {
        $('#message').text(response);
    });
}


        $("#saveButton").click(function() {
            $('td[contenteditable=true]').each(function() {
                if($(this).text() !=  $(this).attr('data-old')) {
                    //Si la cellule a été modifiée
                    updateCell.call(this, $(this).text());
                }
            });
        });

        function addColumn() {
    var tableName = document.getElementById("tableDropdown").value;
    var newColumnName = prompt("Entrez le nom de la nouvelle colonne:");
    if (tableName && newColumnName) {
        $.post('add_column.php', {tableName: tableName, columnName: newColumnName}, function(response) {
            var columns = JSON.parse(response);
            var headerRow = $('#tableData table thead tr');
            headerRow.empty();
            columns.forEach(function(column) {
                headerRow.append('<th>' + column + '</th>');
            });
            loadTableData();  // Recharger les données de la table
        });
    }
}

function addRow() {
    var tableName = document.getElementById("tableDropdown").value;
    if (tableName) {
        // Envoyer la requête AJAX pour ajouter une nouvelle ligne vide
        $.post('add_row.php', {tableName: tableName}, function(response) {
            var messageElement = document.getElementById('message');
            messageElement.textContent = response;
            loadTableData();  // Recharger les données de la table
        });
    }
}

        var statsButton = document.getElementById("statsButton");
        var statsDropdown = document.getElementById("statsDropdown");
        var statsContainer = document.getElementById("statsContainer");
        statsButton.addEventListener("mouseover", function() {
            statsDropdown.style.display = "block";
        });
        statsContainer.addEventListener("mouseleave", function() {
            statsDropdown.style.display = "none";
        });
        statsDropdown.addEventListener("change", function() {
            var selectedOption = statsDropdown.options[statsDropdown.selectedIndex].text;
            console.log("Selected option: " + selectedOption);
        });

        function zoomIn() {
            var table = document.getElementById("myTable");
            var currentFontSize = parseFloat(window.getComputedStyle(table, null).getPropertyValue('font-size'));
            table.style.fontSize = (currentFontSize + 2) + 'px';
        }

        function zoomOut() {
            var table = document.getElementById("myTable");
            var currentFontSize = parseFloat(window.getComputedStyle(table, null).getPropertyValue('font-size'));
            table.style.fontSize = (currentFontSize - 2) + 'px';
        }

        function showStatsForColumn() {
            var tableName = document.getElementById("tableDropdown").value;
            var columnName = document.getElementById("columnName").value;
            var selectedOption = document.getElementById("statsDropdown").value;
            if (tableName && columnName && selectedOption) {
                fetch('calculate_stats.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'tableName=' + tableName + '&columnName=' + columnName + '&statType=' + selectedOption,
                })
                .then(response => response.text())
                .then(data => {
                    var resultElement = document.getElementById("result");
                    resultElement.textContent = "Résultat : " + data;
                });
            }
        }
        function openGraphPage() {
    var tableName = document.getElementById("tableDropdown").value;
    var columnName = document.getElementById("columnName").value;
    if (tableName && columnName) {
        window.open('graphs.php?tableName=' + encodeURIComponent(tableName) + '&columnName=' + encodeURIComponent(columnName), '_blank');
    }
}


function sortColumn(columnName) {
    var tableName = document.getElementById("tableDropdown").value;
    var sortOrder = $(this).data('sort-order') || 'asc';
    
    // Réinitialiser l'ordre de tri des autres colonnes
    $('#tableData th').not(this).data('sort-order', 'asc');
    
    // Inverser l'ordre de tri de la colonne cliquée
    sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
    $(this).data('sort-order', sortOrder);
    
    $.post('sort_column.php', {tableName: tableName, columnName: columnName, sortOrder: sortOrder}, function(response) {
        $('#tableData').html(response);
    });
}

function openGraphPerformance() {
    var tableName = document.getElementById("tableDropdown").value;
    if (tableName) {
        window.open('graphsPerformance.php?tableName=' + encodeURIComponent(tableName), '_blank');
    }
}
    </script>
    <script>
function promptTableName(event) {
    event.preventDefault(); // Empêche la soumission immédiate du formulaire

    var tableName = prompt("Veuillez saisir le nom de la table :");

    if (tableName === null || tableName === "") {
        alert("Veuillez saisir un nom de table valide.");
    } else {
        document.getElementById("tableName").value = tableName;
        document.getElementById("importForm").submit();
    }
}
</script>

</body>
</html>