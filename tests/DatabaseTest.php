<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = getTestDatabaseConnection();
    }

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
        $this->assertEquals('mysql', $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    }

    public function testDatabaseName()
    {
        $stmt = $this->pdo->query("SELECT DATABASE()");
        $dbName = $stmt->fetchColumn();
        $this->assertEquals('carlo_db_test', $dbName);
    }

    public function testTablesExist()
    {
        $tables = ['categories', 'products', 'orders', 'order_items'];
        
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
            $this->assertNotFalse($stmt->fetch(), "Table '$table' should exist");
        }
    }

    public function testCanInsertAndRetrieveData()
    {
        // Test inserting a category
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute(['Test Category', 'Test Description']);
        
        $categoryId = $this->pdo->lastInsertId();
        $this->assertGreaterThan(0, $categoryId);
        
        // Test retrieving the category
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('Test Category', $category['name']);
        $this->assertEquals('Test Description', $category['description']);
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->pdo->exec("DELETE FROM order_items");
        $this->pdo->exec("DELETE FROM orders");
        $this->pdo->exec("DELETE FROM products");
        $this->pdo->exec("DELETE FROM categories");
    }
} 