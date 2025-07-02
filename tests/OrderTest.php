<?php

use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private $pdo;
    private $categoryId;
    private $product1Id;
    private $product2Id;

    protected function setUp(): void
    {
        $this->pdo = getTestDatabaseConnection();
        
        // Set up test data
        $this->setUpTestData();
    }

    private function setUpTestData()
    {
        // Insert test category
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute(['Beverages', 'Drinks and beverages']);
        $this->categoryId = $this->pdo->lastInsertId();

        // Insert test products
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Coffee', 'Hot coffee', 5.00, $this->categoryId, 100]);
        $this->product1Id = $this->pdo->lastInsertId();
        
        $stmt->execute(['Tea', 'Hot tea', 3.50, $this->categoryId, 50]);
        $this->product2Id = $this->pdo->lastInsertId();
    }

    public function testCreateOrder()
    {
        // Create a new order
        $stmt = $this->pdo->prepare("INSERT INTO orders (table_number, status) VALUES (?, ?)");
        $stmt->execute([1, 'pending']);
        $orderId = $this->pdo->lastInsertId();

        $this->assertGreaterThan(0, $orderId);

        // Verify order was created
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(1, $order['table_number']);
        $this->assertEquals('pending', $order['status']);
        $this->assertEquals(0.00, $order['total_amount']);
    }

    public function testAddItemsToOrder()
    {
        // Create order
        $stmt = $this->pdo->prepare("INSERT INTO orders (table_number, status) VALUES (?, ?)");
        $stmt->execute([2, 'pending']);
        $orderId = $this->pdo->lastInsertId();

        // Add items to order
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $this->product1Id, 2, 5.00]); // 2 coffees
        $stmt->execute([$orderId, $this->product2Id, 1, 3.50]); // 1 tea

        // Calculate total
        $stmt = $this->pdo->prepare("SELECT SUM(quantity * price) as total FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $total = $stmt->fetchColumn();

        $expectedTotal = (2 * 5.00) + (1 * 3.50); // 13.50
        $this->assertEquals($expectedTotal, $total);

        // Update order total
        $stmt = $this->pdo->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
        $stmt->execute([$total, $orderId]);

        // Verify order total
        $stmt = $this->pdo->prepare("SELECT total_amount FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $orderTotal = $stmt->fetchColumn();

        $this->assertEquals($expectedTotal, $orderTotal);
    }

    public function testUpdateOrderStatus()
    {
        // Create order
        $stmt = $this->pdo->prepare("INSERT INTO orders (table_number, status) VALUES (?, ?)");
        $stmt->execute([3, 'pending']);
        $orderId = $this->pdo->lastInsertId();

        // Update status to preparing
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute(['preparing', $orderId]);

        // Verify status update
        $stmt = $this->pdo->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $status = $stmt->fetchColumn();

        $this->assertEquals('preparing', $status);
    }

    public function testOrderStatusTransitions()
    {
        $validTransitions = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];

        foreach ($validTransitions as $fromStatus => $toStatuses) {
            $stmt = $this->pdo->prepare("INSERT INTO orders (table_number, status) VALUES (?, ?)");
            $stmt->execute([4, $fromStatus]);
            $orderId = $this->pdo->lastInsertId();

            foreach ($toStatuses as $toStatus) {
                $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->execute([$toStatus, $orderId]);

                $stmt = $this->pdo->prepare("SELECT status FROM orders WHERE id = ?");
                $stmt->execute([$orderId]);
                $currentStatus = $stmt->fetchColumn();

                $this->assertEquals($toStatus, $currentStatus, 
                    "Order should be able to transition from $fromStatus to $toStatus");
            }
        }
    }

    public function testOrderWithMultipleItems()
    {
        // Create order
        $stmt = $this->pdo->prepare("INSERT INTO orders (table_number, status) VALUES (?, ?)");
        $stmt->execute([5, 'pending']);
        $orderId = $this->pdo->lastInsertId();

        // Add multiple items
        $items = [
            [$this->product1Id, 3, 5.00], // 3 coffees
            [$this->product2Id, 2, 3.50], // 2 teas
        ];

        foreach ($items as $item) {
            $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $item[0], $item[1], $item[2]]);
        }

        // Get all items for this order
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $orderItems);

        // Calculate total
        $total = 0;
        foreach ($orderItems as $item) {
            $total += $item['quantity'] * $item['price'];
        }

        $expectedTotal = (3 * 5.00) + (2 * 3.50); // 15.00 + 7.00 = 22.00
        $this->assertEquals($expectedTotal, $total);
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