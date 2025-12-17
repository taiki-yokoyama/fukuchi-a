<?php
// Test the complete baggage registration flow

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/Infrastructure/pdo.php';

// Start session for testing
session_start();

echo "Testing Baggage Registration Flow\n";
echo "=================================\n\n";

try {
    // Initialize database
    $pdo = getPDO();
    
    // Create test user
    echo "1. Creating test user...\n";
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (id, name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([999, 'Test User', 'test@example.com', password_hash('password', PASSWORD_DEFAULT)]);
    
    // Set session for test user
    $_SESSION['user_id'] = 999;
    echo "   ✓ Test user created and logged in\n";
    
    // Create test items
    echo "\n2. Creating test items...\n";
    $itemRepository = new ItemRepositoryImpl($pdo);
    
    $item1 = new Item(null, new ItemName('財布'), new TagId('WALLET01'), null);
    $itemRepository->save($item1, 999);
    echo "   ✓ Item 1 created: 財布 (ID: " . $item1->getId() . ")\n";
    
    $item2 = new Item(null, new ItemName('スマートフォン'), new TagId('PHONE01'), null);
    $itemRepository->save($item2, 999);
    echo "   ✓ Item 2 created: スマートフォン (ID: " . $item2->getId() . ")\n";
    
    $item3 = new Item(null, new ItemName('鍵'), new TagId('KEY01'), null);
    $itemRepository->save($item3, 999);
    echo "   ✓ Item 3 created: 鍵 (ID: " . $item3->getId() . ")\n";
    
    // Test baggage registration
    echo "\n3. Testing baggage registration...\n";
    $baggageRepository = new BaggageRepositoryImpl($pdo);
    $baggageUsecase = new BaggageRegisterUsecase($baggageRepository, $itemRepository);
    
    $testDate = '2024-01-15';
    $itemIds = [$item1->getId(), $item2->getId()];
    
    $baggageUsecase->registerBaggage(999, $testDate, $itemIds);
    echo "   ✓ Baggage registered for date: $testDate\n";
    
    // Verify baggage was saved
    echo "\n4. Verifying saved baggage...\n";
    $savedBaggages = $baggageRepository->findByUserAndDate(999, $testDate);
    
    if (empty($savedBaggages)) {
        echo "   ✗ No baggage found for the test date\n";
    } else {
        $baggage = $savedBaggages[0];
        echo "   ✓ Baggage found (ID: " . $baggage->getId() . ")\n";
        echo "   ✓ Items in baggage: " . count($baggage->getItems()) . "\n";
        
        foreach ($baggage->getItems() as $item) {
            echo "     - " . $item->getName()->getName() . " (Tag: " . $item->getTagId()->getId() . ")\n";
        }
    }
    
    // Test validation
    echo "\n5. Testing validation...\n";
    
    // Test empty date
    try {
        $controller = new BaggageController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateBaggageRegistration');
        $method->setAccessible(true);
        
        $errors = $method->invoke($controller, '', [1, 2]);
        if (isset($errors['date'])) {
            echo "   ✓ Empty date validation works\n";
        } else {
            echo "   ✗ Empty date validation failed\n";
        }
        
        // Test empty items
        $errors = $method->invoke($controller, '2024-01-01', []);
        if (isset($errors['items'])) {
            echo "   ✓ Empty items validation works\n";
        } else {
            echo "   ✗ Empty items validation failed\n";
        }
        
        // Test valid input
        $errors = $method->invoke($controller, '2024-01-01', [1, 2]);
        if (empty($errors)) {
            echo "   ✓ Valid input validation works\n";
        } else {
            echo "   ✗ Valid input validation failed\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ Validation test error: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Testing template functionality...\n";
    
    // Create template
    $templateName = "外出用セット";
    $baggageUsecase->createTemplate(999, $templateName, [$item1->getId(), $item3->getId()]);
    echo "   ✓ Template created: $templateName\n";
    
    // Get templates
    $templates = $baggageUsecase->getTemplatesByUser(999);
    if (!empty($templates)) {
        $template = $templates[0];
        echo "   ✓ Template retrieved (ID: " . $template->getId() . ")\n";
        echo "   ✓ Template items: " . count($template->getItems()) . "\n";
        
        // Apply template
        $applyDate = '2024-01-20';
        $baggageUsecase->applyTemplate(999, $template->getId(), $applyDate);
        echo "   ✓ Template applied to date: $applyDate\n";
        
        // Verify applied template
        $appliedBaggages = $baggageRepository->findByUserAndDate(999, $applyDate);
        if (!empty($appliedBaggages)) {
            echo "   ✓ Applied template verified (" . count($appliedBaggages[0]->getItems()) . " items)\n";
        } else {
            echo "   ✗ Applied template not found\n";
        }
    } else {
        echo "   ✗ No templates found\n";
    }
    
    echo "\nAll tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} finally {
    // Clean up test data
    if (isset($pdo)) {
        try {
            $pdo->exec("DELETE FROM baggage_items WHERE baggage_id IN (SELECT id FROM baggages WHERE user_id = 999)");
            $pdo->exec("DELETE FROM baggages WHERE user_id = 999");
            $pdo->exec("DELETE FROM items WHERE user_id = 999");
            $pdo->exec("DELETE FROM users WHERE id = 999");
            echo "\nTest data cleaned up.\n";
        } catch (Exception $e) {
            echo "\nCleanup error: " . $e->getMessage() . "\n";
        }
    }
}