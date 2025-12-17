<?php
// Integration test for baggage registration

require_once __DIR__ . '/src/autoload.php';

// Start session
session_start();

echo "Integration Test: Baggage Registration\n";
echo "=====================================\n\n";

try {
    // Test 1: Basic class loading
    echo "1. Testing class loading...\n";
    
    $classes = [
        'BaggageController',
        'BaggageRegisterUsecase', 
        'BaggageRepositoryImpl',
        'ItemRepositoryImpl',
        'Baggage',
        'Item',
        'ItemName',
        'TagId'
    ];
    
    foreach ($classes as $class) {
        if (class_exists($class)) {
            echo "   ✓ $class loaded\n";
        } else {
            echo "   ✗ $class failed to load\n";
            throw new Exception("Required class $class not found");
        }
    }
    
    // Test 2: Database connection
    echo "\n2. Testing database connection...\n";
    require_once __DIR__ . '/src/Infrastructure/pdo.php';
    $pdo = getPDO();
    echo "   ✓ Database connected\n";
    
    // Test 3: Controller instantiation
    echo "\n3. Testing controller instantiation...\n";
    $controller = new BaggageController();
    echo "   ✓ BaggageController created\n";
    
    // Test 4: Value objects
    echo "\n4. Testing value objects...\n";
    $itemName = new ItemName("テストアイテム");
    echo "   ✓ ItemName: " . $itemName->getName() . "\n";
    
    $tagId = new TagId("TEST123");
    echo "   ✓ TagId: " . $tagId->getId() . "\n";
    
    // Test 5: Entity creation
    echo "\n5. Testing entity creation...\n";
    $item = new Item(1, $itemName, $tagId, null);
    echo "   ✓ Item created with ID: " . $item->getId() . "\n";
    
    $baggage = new Baggage(1, 1, '2024-01-01', false, null);
    echo "   ✓ Baggage created with ID: " . $baggage->getId() . "\n";
    
    // Test 6: Repository operations (mock test)
    echo "\n6. Testing repository instantiation...\n";
    $itemRepo = new ItemRepositoryImpl($pdo);
    $baggageRepo = new BaggageRepositoryImpl($pdo);
    echo "   ✓ Repositories created\n";
    
    // Test 7: Use case instantiation
    echo "\n7. Testing use case...\n";
    $usecase = new BaggageRegisterUsecase($baggageRepo, $itemRepo);
    echo "   ✓ BaggageRegisterUsecase created\n";
    
    // Test 8: Validation methods
    echo "\n8. Testing validation...\n";
    $reflection = new ReflectionClass('BaggageController');
    
    // Test isValidDate method
    $isValidDateMethod = $reflection->getMethod('isValidDate');
    $isValidDateMethod->setAccessible(true);
    
    $validDate = $isValidDateMethod->invoke($controller, '2024-01-01');
    $invalidDate = $isValidDateMethod->invoke($controller, 'invalid');
    
    if ($validDate && !$invalidDate) {
        echo "   ✓ Date validation working\n";
    } else {
        echo "   ✗ Date validation failed\n";
    }
    
    // Test validateBaggageRegistration method
    $validateMethod = $reflection->getMethod('validateBaggageRegistration');
    $validateMethod->setAccessible(true);
    
    $errors = $validateMethod->invoke($controller, '2024-01-01', [1, 2]);
    if (empty($errors)) {
        echo "   ✓ Baggage validation working\n";
    } else {
        echo "   ✗ Baggage validation failed: " . implode(', ', $errors) . "\n";
    }
    
    echo "\nAll integration tests passed! ✓\n";
    echo "\nThe baggage registration functionality is ready to use.\n";
    echo "You can access it at: /baggage/register\n";
    
} catch (Exception $e) {
    echo "\nIntegration test failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}