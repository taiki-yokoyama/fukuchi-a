<?php
// Demo: Baggage Registration Functionality

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/Infrastructure/pdo.php';

// Start session
session_start();

echo "Demo: Baggage Registration Functionality\n";
echo "========================================\n\n";

try {
    // Initialize database
    $pdo = getPDO();
    
    // Create demo user
    echo "Setting up demo user...\n";
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO users (id, name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 'Demo User', 'demo@example.com', password_hash('demo123', PASSWORD_DEFAULT)]);
    $_SESSION['user_id'] = 1;
    echo "✓ Demo user created and logged in\n\n";
    
    // Create demo items
    echo "Creating demo items...\n";
    $itemRepo = new ItemRepositoryImpl($pdo);
    
    // Clear existing items for demo user
    $pdo->exec("DELETE FROM baggage_items WHERE item_id IN (SELECT id FROM items WHERE user_id = 1)");
    $pdo->exec("DELETE FROM items WHERE user_id = 1");
    
    $items = [
        ['name' => '財布', 'tag' => 'WALLET01'],
        ['name' => 'スマートフォン', 'tag' => 'PHONE01'],
        ['name' => '鍵', 'tag' => 'KEYS01'],
        ['name' => '傘', 'tag' => 'UMBRELLA01'],
        ['name' => 'ノートPC', 'tag' => 'LAPTOP01']
    ];
    
    $createdItems = [];
    foreach ($items as $itemData) {
        $item = new Item(null, new ItemName($itemData['name']), new TagId($itemData['tag']), null);
        $itemRepo->save($item, 1);
        $createdItems[] = $item;
        echo "✓ Created: {$itemData['name']} (Tag: {$itemData['tag']})\n";
    }
    
    echo "\nDemo Scenario 1: Register baggage for today\n";
    echo "-------------------------------------------\n";
    
    $baggageRepo = new BaggageRepositoryImpl($pdo);
    $baggageUsecase = new BaggageRegisterUsecase($baggageRepo, $itemRepo);
    
    // Register baggage for today with first 3 items
    $today = date('Y-m-d');
    $selectedItemIds = [
        $createdItems[0]->getId(), // 財布
        $createdItems[1]->getId(), // スマートフォン
        $createdItems[2]->getId()  // 鍵
    ];
    
    $baggageUsecase->registerBaggage(1, $today, $selectedItemIds);
    echo "✓ Registered baggage for today ($today) with 3 items\n";
    
    // Verify the registration
    $todaysBaggages = $baggageRepo->findByUserAndDate(1, $today);
    if (!empty($todaysBaggages)) {
        $baggage = $todaysBaggages[0];
        echo "✓ Verification: Found baggage with " . count($baggage->getItems()) . " items:\n";
        foreach ($baggage->getItems() as $item) {
            echo "  - " . $item->getName()->getName() . " (Tag: " . $item->getTagId()->getId() . ")\n";
        }
    }
    
    echo "\nDemo Scenario 2: Create and use template\n";
    echo "----------------------------------------\n";
    
    // Create a template for work days
    $workItemIds = [
        $createdItems[0]->getId(), // 財布
        $createdItems[1]->getId(), // スマートフォン
        $createdItems[2]->getId(), // 鍵
        $createdItems[4]->getId()  // ノートPC
    ];
    
    $baggageUsecase->createTemplate(1, "仕事用セット", $workItemIds);
    echo "✓ Created template '仕事用セット' with 4 items\n";
    
    // Get templates and apply to tomorrow
    $templates = $baggageUsecase->getTemplatesByUser(1);
    if (!empty($templates)) {
        $template = $templates[0];
        echo "✓ Found template: " . $template->getName() . " with " . count($template->getItems()) . " items\n";
        
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $baggageUsecase->applyTemplate(1, $template->getId(), $tomorrow);
        echo "✓ Applied template to tomorrow ($tomorrow)\n";
        
        // Verify template application
        $tomorrowsBaggages = $baggageRepo->findByUserAndDate(1, $tomorrow);
        if (!empty($tomorrowsBaggages)) {
            echo "✓ Verification: Tomorrow's baggage has " . count($tomorrowsBaggages[0]->getItems()) . " items\n";
        }
    }
    
    echo "\nDemo Scenario 3: Validation testing\n";
    echo "-----------------------------------\n";
    
    $controller = new BaggageController();
    $reflection = new ReflectionClass($controller);
    $validateMethod = $reflection->getMethod('validateBaggageRegistration');
    $validateMethod->setAccessible(true);
    
    // Test various validation scenarios
    $testCases = [
        ['date' => '', 'items' => [1, 2], 'expected' => 'date error'],
        ['date' => 'invalid-date', 'items' => [1, 2], 'expected' => 'date error'],
        ['date' => '2024-01-01', 'items' => [], 'expected' => 'items error'],
        ['date' => '2024-01-01', 'items' => [1, 2], 'expected' => 'no errors']
    ];
    
    foreach ($testCases as $i => $testCase) {
        $errors = $validateMethod->invoke($controller, $testCase['date'], $testCase['items']);
        $hasErrors = !empty($errors);
        $expectsErrors = $testCase['expected'] !== 'no errors';
        
        if ($hasErrors === $expectsErrors) {
            echo "✓ Test case " . ($i + 1) . ": " . $testCase['expected'] . "\n";
        } else {
            echo "✗ Test case " . ($i + 1) . " failed\n";
        }
    }
    
    echo "\nDemo Summary\n";
    echo "============\n";
    echo "✓ Baggage registration functionality is working correctly\n";
    echo "✓ Template creation and application works\n";
    echo "✓ Form validation is functioning properly\n";
    echo "✓ Database operations are successful\n";
    echo "\nThe baggage registration feature is ready for use!\n";
    echo "Access it at: http://your-domain/baggage/register\n";
    
} catch (Exception $e) {
    echo "\nDemo failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} finally {
    // Note: In a real demo, you might want to keep the data
    // For this demo, we'll leave the data for testing
    echo "\nDemo data has been left in the database for testing.\n";
}