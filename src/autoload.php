<?php

// Simple autoloader for the application
spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/';
    
    // Map class names to file paths
    $classMap = [
        // Domain Entities
        'User' => 'Domain/Entity/User.php',
        'Item' => 'Domain/Entity/Item.php',
        'Baggage' => 'Domain/Entity/Baggage.php',
        'BaggageItem' => 'Domain/Entity/BaggageItem.php',
        
        // Value Objects
        'ItemName' => 'Domain/ValueObject/ItemName.php',
        'TagId' => 'Domain/ValueObject/TagId.php',
        
        // Repository Interfaces
        'UserRepository' => 'Domain/Repository/UserRepository.php',
        'ItemRepository' => 'Domain/Repository/ItemRepository.php',
        'BaggageRepository' => 'Domain/Repository/BaggageRepository.php',
        'BaggageItemRepository' => 'Domain/Repository/BaggageItemRepository.php',
        
        // Infrastructure
        'UserRepositoryImpl' => 'Infrastructure/UserRepositoryImpl.php',
        'ItemRepositoryImpl' => 'Infrastructure/ItemRepositoryImpl.php',
        'BaggageRepositoryImpl' => 'Infrastructure/BaggageRepositoryImpl.php',
        'BaggageItemRepositoryImpl' => 'Infrastructure/BaggageItemRepositoryImpl.php',
        'DatabaseConnection' => 'Infrastructure/pdo.php',
        
        // Use Cases
        'TopUsecase' => 'Application/Usecase/TopUsecase.php',
        'BaggageRegisterUsecase' => 'Application/Usecase/BaggageRegisterUsecase.php',
        'ItemRegisterUsecase' => 'Application/Usecase/ItemRegisterUsecase.php',
        'UserRegisterUsecase' => 'Application/Usecase/UserRegisterUsecase.php',
        
        // Controllers
        'UserController' => 'Controllers/UserController.php',
        'BaggageController' => 'Controllers/BaggageController.php',
        'ItemController' => 'Controllers/ItemController.php',
        
        // Config
        'Config' => 'Config/config.php',
    ];
    
    if (isset($classMap[$className])) {
        $file = $baseDir . $classMap[$className];
        if (file_exists($file)) {
            require_once $file;
        }
    }
});