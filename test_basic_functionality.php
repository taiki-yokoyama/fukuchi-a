<?php
// Basic functionality test

require_once __DIR__ . '/src/autoload.php';

echo "Testing Basic Functionality\n";
echo "===========================\n\n";

try {
    // Test Value Objects
    echo "1. Testing Value Objects...\n";
    
    $itemName = new ItemName("テストアイテム");
    echo "   ✓ ItemName created: " . $itemName->getName() . "\n";
    
    $tagId = new TagId("ABC123");
    echo "   ✓ TagId created: " . $tagId->getId() . "\n";
    
    // Test Entities
    echo "\n2. Testing Entities...\n";
    
    $item = new Item(1, $itemName, $tagId, null);
    echo "   ✓ Item created with ID: " . $item->getId() . "\n";
    
    $baggage = new Baggage(1, 1, '2024-01-01', false, null);
    echo "   ✓ Baggage created with ID: " . $baggage->getId() . "\n";
    
    // Test adding item to baggage
    $baggage->addItem($item);
    echo "   ✓ Item added to baggage. Items count: " . count($baggage->getItems()) . "\n";
    
    echo "\nAll basic tests passed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}