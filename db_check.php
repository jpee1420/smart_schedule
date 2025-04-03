<?php
/**
 * Database Check Utility
 * 
 * Include this file at the top of any PHP file that requires database access.
 * It ensures that database installation is checked before any database operations.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config
require_once 'config.php';

// Check if the database needs installation
if (defined('DB_NEEDS_INSTALLATION') && DB_NEEDS_INSTALLATION) {
    header('Location: ' . BASE_URL . '/install/index.php');
    exit;
}

// Check if there's a database connection error
if (defined('DB_CONNECTION_ERROR') && DB_CONNECTION_ERROR) {
    die("Database connection error. Please check your database settings or run the <a href='" . BASE_URL . "/install/index.php'>installation wizard</a>.");
}
?> 