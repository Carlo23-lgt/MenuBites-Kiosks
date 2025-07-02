<?php

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private $pdo;
    private $categoryId;

    protected function setUp(): void
    {
        $this->pdo = getTestDatabaseConnection();
        
        // Set up test category
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute(['Test Category', 'Test Description']);
        $this->categoryId = $this->pdo->lastInsertId();
    }

    public function testCreateProduct()
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Test Product', 'Test Description', 10.50, $this->categoryId, 25]);
        $productId = $this->pdo->lastInsertId();

        $this->assertGreaterThan(0, $productId);

        // Verify product was created
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Test Product', $product['name']);
        $this->assertEquals('Test Description', $product['description']);
        $this->assertEquals(10.50, $product['price']);
        $this->assertEquals($this->categoryId, $product['category_id']);
        $this->assertEquals(25, $product['stock']);
    }

    public function testUpdateProduct()
    {
        // Create product
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Original Name', 'Original Description', 5.00, $this->categoryId, 10]);
        $productId = $this->pdo->lastInsertId();

        // Update product
        $stmt = $this->pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->execute(['Updated Name', 'Updated Description', 7.50, 15, $productId]);

        // Verify update
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Updated Name', $product['name']);
        $this->assertEquals('Updated Description', $product['description']);
        $this->assertEquals(7.50, $product['price']);
        $this->assertEquals(15, $product['stock']);
    }

    public function testProductPriceValidation()
    {
        // Test with valid price
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Valid Product', 'Valid Description', 0.01, $this->categoryId, 1]);
        $productId = $this->pdo->lastInsertId();

        $this->assertGreaterThan(0, $productId);

        // Test with zero price (should be allowed for free items)
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Free Product', 'Free Description', 0.00, $this->categoryId, 1]);
        $freeProductId = $this->pdo->lastInsertId();

        $this->assertGreaterThan(0, $freeProductId);
    }

    public function testProductStockManagement()
    {
        // Create product with initial stock
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Stock Product', 'Stock Description', 5.00, $this->categoryId, 50]);
        $productId = $this->pdo->lastInsertId();

        // Reduce stock
        $stmt = $this->pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([10, $productId]);

        // Verify stock reduction
        $stmt = $this->pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $stock = $stmt->fetchColumn();

        $this->assertEquals(40, $stock);

        // Add stock
        $stmt = $this->pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([5, $productId]);

        // Verify stock addition
        $stmt = $this->pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $stock = $stmt->fetchColumn();

        $this->assertEquals(45, $stock);
    }

    public function testGetProductsByCategory()
    {
        // Create another category
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute(['Category 2', 'Second Category']);
        $category2Id = $this->pdo->lastInsertId();

        // Create products in different categories
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Product 1', 'Description 1', 5.00, $this->categoryId, 10]);
        $stmt->execute(['Product 2', 'Description 2', 7.50, $this->categoryId, 15]);
        $stmt->execute(['Product 3', 'Description 3', 10.00, $category2Id, 20]);

        // Get products by first category
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$this->categoryId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $products);

        // Get products by second category
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$category2Id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $products);
    }

    public function testProductSearch()
    {
        // Create products with different names
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Coffee Latte', 'Hot coffee with milk', 5.50, $this->categoryId, 10]);
        $stmt->execute(['Espresso', 'Strong coffee', 3.50, $this->categoryId, 15]);
        $stmt->execute(['Tea Green', 'Green tea', 4.00, $this->categoryId, 20]);
        $stmt->execute(['Coffee Mocha', 'Coffee with chocolate', 6.00, $this->categoryId, 12]);

        // Search for coffee products
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
        $stmt->execute(['%Coffee%']);
        $coffeeProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $coffeeProducts);

        // Search for tea products
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
        $stmt->execute(['%Tea%']);
        $teaProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $teaProducts);
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