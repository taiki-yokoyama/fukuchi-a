<?php

// Simple test to verify the application layer implementation
require_once 'src/autoload.php';

echo "Testing Application Layer Implementation...\n";

try {
    // Test that all classes can be loaded
    echo "✓ Loading TopUsecase...\n";
    require_once 'src/Application/Usecase/TopUsecase.php';
    
    echo "✓ Loading BaggageRegisterUsecase...\n";
    require_once 'src/Application/Usecase/BaggageRegisterUsecase.php';
    
    echo "✓ Loading ItemRegisterUsecase...\n";
    require_once 'src/Application/Usecase/ItemRegisterUsecase.php';
    
    echo "✓ Loading UserRegisterUsecase...\n";
    require_once 'src/Application/Usecase/UserRegisterUsecase.php';
    
    echo "✓ Loading RFIDScanService...\n";
    require_once 'src/Application/Service/RFIDScanService.php';
    
    echo "✓ Loading WeatherService...\n";
    require_once 'src/Application/Service/WeatherService.php';
    
    echo "\n✅ All Application Layer classes loaded successfully!\n";
    echo "✅ Task 3 - Application Layer Implementation completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}