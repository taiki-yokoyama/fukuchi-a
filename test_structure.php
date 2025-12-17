<?php

// Basic structure test
echo "Testing domain structure...\n";

try {
    // Load autoloader
    require_once 'src/autoload.php';
    
    // Test value objects
    $itemName = new ItemName("テストアイテム");
    echo "ItemName created: " . $itemName->getName() . "\n";
    
    $tagId = new TagId("ABC123");
    echo "TagId created: " . $tagId->getId() . "\n";
    
    // Test entities
    $user = new User(null, "テストユーザー", "test@example.com", User::hashPassword("password"));
    echo "User created: " . $user->getName() . "\n";
    
    $item = new Item(null, $itemName, $tagId, null);
    echo "Item created: " . $item->getName()->getName() . "\n";
    
    $baggage = new Baggage(null, 1, "2024-01-01", false, null);
    echo "Baggage created for user: " . $baggage->getUserId() . "\n";
    
    // Test database connection
    $pdo = DatabaseConnection::getInstance();
    echo "Database connection established successfully!\n";
    
    echo "All domain objects and infrastructure created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}