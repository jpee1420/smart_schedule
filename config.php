<?php
// config.php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'smart_schedule';

// Define base URL for the application
define('BASE_URL', '/smart_schedule');

// Connect without database first to check if it exists
$tempConn = new mysqli($db_host, $db_user, $db_pass);

if ($tempConn->connect_error) {
    define('DB_CONNECTION_ERROR', true);
} else {
    // Check if database exists
    $dbExists = $tempConn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
    
    if ($dbExists->num_rows == 0) {
        define('DB_NEEDS_INSTALLATION', true);
    } else {
        // Connect to the database to check for required tables
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn->connect_error) {
            define('DB_CONNECTION_ERROR', true);
        } else {
            // Check for required tables
            $requiredTables = ['professors', 'rooms', 'courses', 'schedules'];
            $missingTables = [];
            
            foreach ($requiredTables as $table) {
                $tableExists = $conn->query("SHOW TABLES LIKE '$table'");
                if ($tableExists->num_rows == 0) {
                    $missingTables[] = $table;
                }
            }
            
            if (!empty($missingTables)) {
                define('DB_NEEDS_INSTALLATION', true);
                error_log("Missing required tables: " . implode(', ', $missingTables));
            } else {
                define('DB_NEEDS_INSTALLATION', false);
                define('DB_CONNECTION_ERROR', false);
            }
        }
    }
    
    $tempConn->close();
}

// Check if the installation is complete
if (!defined('DB_CONNECTION_ERROR')) {
    define('DB_CONNECTION_ERROR', false);
}