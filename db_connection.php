<?php
/**
 * Centralized Database Connection File
 * This file contains all database configuration and connection logic
 * Include this file in any PHP script that needs database access
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USER', 'sednaris_bhavi');
define('DB_PASS', '!dKyAd9..{Ux');
define('DB_NAME', 'sednaris_bhavi');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * @return PDO Database connection instance
 * @throws PDOException If connection fails
 */
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}

/**
 * Get database connection for legacy code compatibility
 * @return PDO Database connection instance
 * @deprecated Use getDBConnection() instead
 */
function connectDB() {
    return getDBConnection();
}
?>