<?php
/**
 * Centralized Database Configuration for Kaggle Project
 * This file handles database connection using Docker environment variables
 */

// Database configuration from environment variables
$db_config = [
    'host' => $_ENV['DB_HOST'] ?? 'db',
    'name' => $_ENV['DB_NAME'] ?? 'kaggle_project',
    'user' => $_ENV['DB_USER'] ?? 'kaggle_user',
    'password' => $_ENV['DB_PASSWORD'] ?? 'kaggle_password_123',
    'port' => $_ENV['DB_PORT'] ?? '3306'
];

/**
 * Create database connection using MySQLi
 * @return mysqli Database connection object
 * @throws Exception If connection fails
 */
function getDatabaseConnection() {
    global $db_config;
    
    $connection = new mysqli(
        $db_config['host'],
        $db_config['user'],
        $db_config['password'],
        $db_config['name'],
        $db_config['port']
    );
    
    if ($connection->connect_error) {
        throw new Exception("Database connection failed: " . $connection->connect_error);
    }
    
    // Set charset to UTF-8
    $connection->set_charset("utf8");
    
    return $connection;
}

/**
 * Create PDO database connection
 * @return PDO Database connection object
 * @throws Exception If connection fails
 */
function getPDOConnection() {
    global $db_config;
    
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=utf8",
        $db_config['host'],
        $db_config['port'],
        $db_config['name']
    );
    
    try {
        $pdo = new PDO($dsn, $db_config['user'], $db_config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("PDO connection failed: " . $e->getMessage());
    }
}

/**
 * Execute a query with error handling
 * @param mysqli $connection Database connection
 * @param string $query SQL query
 * @return mysqli_result|bool Query result
 * @throws Exception If query fails
 */
function executeQuery($connection, $query) {
    $result = $connection->query($query);
    if (!$result) {
        throw new Exception("Query failed: " . $connection->error);
    }
    return $result;
}

/**
 * Close database connection safely
 * @param mysqli $connection Database connection to close
 */
function closeConnection($connection) {
    if ($connection && !$connection->connect_error) {
        $connection->close();
    }
}

// Export configuration for direct access if needed
$GLOBALS['db_config'] = $db_config;
?>