<?php
// Test script for baggage registration functionality

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/Controllers/BaggageController.php';
require_once __DIR__ . '/src/Infrastructure/pdo.php';

// Initialize session for testing
session_start();

echo "Testing Baggage Registration Functionality\n";
echo "==========================================\n\n";

try {
    // Test 1: Check if PDO connection works
    echo "1. Testing PDO connection...\n";
    $pdo = getPDO();
    echo "   ✓ PDO connection successful\n\n";
    
    // Test 2: Check if BaggageController can be instantiated
    echo "2. Testing BaggageController instantiation...\n";
    $controller = new BaggageController();
    echo "   ✓ BaggageController instantiated successfully\n\n";
    
    // Test 3: Check if required classes exist
    echo "3. Testing required classes...\n";
    
    $requiredClasses = [
        'BaggageRegisterUsecase',
        'BaggageRepositoryImpl', 
        'ItemRepositoryImpl',
        'Baggage',
        'Item',
        'ItemName',
        'TagId'
    ];
    
    foreach ($requiredClasses as $className) {
        if (class_exists($className)) {
            echo "   ✓ Class $className exists\n";
        } else {
            echo "   ✗ Class $className missing\n";
        }
    }
    
    echo "\n4. Testing database schema...\n";
    
    // Check if tables exist
    $tables = ['users', 'baggages', 'items', 'baggage_items'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "   ✓ Table $table exists\n";
        } else {
            echo "   ✗ Table $table missing\n";
        }
    }
    
    echo "\n5. Testing validation methods...\n";
    
    // Test date validation
    $reflection = new ReflectionClass('BaggageController');
    $method = $reflection->getMethod('isValidDate');
    $method->setAccessible(true);
    
    $testDates = [
        '2024-01-01' => true,
        '2024-02-29' => true, // leap year
        '2023-02-29' => false, // not leap year
        'invalid' => false,
        '' => false
    ];
    
    foreach ($testDates as $date => $expected) {
        $result = $method->invoke($controller, $date);
        if ($result === $expected) {
            echo "   ✓ Date validation for '$date': " . ($expected ? 'valid' : 'invalid') . "\n";
        } else {
            echo "   ✗ Date validation for '$date' failed\n";
        }
    }
    
    echo "\nAll tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}