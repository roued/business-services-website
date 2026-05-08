<?php
/**
 * Database Configuration
 * إعدادات قاعدة البيانات
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'business_services');
define('DB_PORT', 3306);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Set charset to UTF-8
$conn->set_charset('utf8mb4');

// Define constants for security
define('SITE_URL', 'http://localhost/business-services-website/');
define('ADMIN_URL', SITE_URL . 'admin/');
define('API_URL', SITE_URL . 'api/');
define('SECRET_KEY', 'your-secret-key-change-this');

// Error handling
define('DEBUG_MODE', true);
if (!DEBUG_MODE) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

?>