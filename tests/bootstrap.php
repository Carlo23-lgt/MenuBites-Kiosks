<?php
/**
 * Bootstrap file for PHPUnit tests
 * This file is loaded before any tests run
 */

// Set error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set testing environment
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Include Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Include your main configuration
require_once __DIR__ . '/../includes/config.php';

// Set up test database connection
function getTestDatabaseConnection() {
    $host = "localhost";
    $dbname = "carlo_db_test"; // Test database
    $username = "root";
    $password = "";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Test database connection failed: " . $e->getMessage());
    }
}

// Helper function to reset test database
function resetTestDatabase() {
    $pdo = getTestDatabaseConnection();
    
    // Drop and recreate test database
    $pdo->exec("DROP DATABASE IF EXISTS carlo_db_test");
    $pdo->exec("CREATE DATABASE carlo_db_test");
    $pdo->exec("USE carlo_db_test");
    
    // Here you would run your database migrations or schema setup
    // For now, we'll create basic tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            category_id INT,
            image VARCHAR(255),
            stock INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_number INT,
            status ENUM('pending', 'preparing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
            total_amount DECIMAL(10,2) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT,
            product_id INT,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )
    ");
}

// Initialize test database if it doesn't exist
try {
    getTestDatabaseConnection();
} catch (Exception $e) {
    // Create test database if it doesn't exist
    $host = "localhost";
    $username = "root";
    $password = "";
    
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS carlo_db_test");
    
    // Set up schema
    resetTestDatabase();
} 