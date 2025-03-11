<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'news_website';
$db_user = 'root';
$db_pass = '';

// DSN (Data Source Name)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

// PDO options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Create PDO instance
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // For production, you might want to log this instead of displaying
    die("Connection failed: " . $e->getMessage());
}

