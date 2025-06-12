<?php
require_once 'config.php';

try {
    $conn = getDatabaseConnection();
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$sql = "SELECT * FROM merged_data WHERE RAND() <= 0.01";  // Ceci renverra environ 1% de vos lignes

$time_before = microtime(true);
$result = mysqli_query($conn, $sql);
$time_after = microtime(true);

$response_time = $time_after - $time_before;
echo "Temps de réponse : " . $response_time . " secondes.";

closeConnection($conn);
?>