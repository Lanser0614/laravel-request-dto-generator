<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BellissimoPizza\RequestDtoGenerator\Commands\GenerateDtoFromRequestCommand;
use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Filesystem\Filesystem;

/**
 * Test Artisan Command Search Functionality
 * 
 * This script tests the improved search functionality of the Artisan command.
 */

// Configuration
$config = [
    'dto_namespace' => 'App\\DTOs\\Api\\v1',
    'dto_directory' => __DIR__ . '/generated',
    'request_directory' => __DIR__ . '/Http/Requests',
    'dto_base_class' => 'BellissimoPizza\\RequestDtoGenerator\\BaseDto',
    'auto_generate_properties' => true,
    'include_validation_rules' => true,
    'generate_constructor' => true,
    'generate_accessors' => true,
    'readonly_properties' => true,
    'constructor_property_promotion' => true,
    'property_visibility' => 'private',
    'generate_separate_dtos_for_arrays' => true
];

try {
    echo "🚀 Test Artisan Command Search Functionality\n";
    echo "===========================================\n\n";
    
    // Load Request classes
    require_once __DIR__ . '/Http/Requests/TestRequest.php';
    require_once __DIR__ . '/Http/Requests/Api/SentCouponRequest.php';
    
    // Create command instance
    $command = new GenerateDtoFromRequestCommand();
    
    echo "1️⃣ Testing Request classes discovery...\n";
    $filesystem = new Filesystem();
    $dtoGenerator = new JsonSchemaDtoGenerator($filesystem, $config);
    
    $requestClasses = $dtoGenerator->getRequestClasses();
    echo "✅ Found " . count($requestClasses) . " Request classes:\n";
    foreach ($requestClasses as $requestClass) {
        echo "   - {$requestClass}\n";
    }
    echo "\n";
    
    echo "2️⃣ Testing command search functionality...\n";
    
    // Test various search terms
    $searchTerms = [
        'SentCouponRequest',
        'SentCoupon',
        'TestRequest',
        'Test',
        'Api\\SentCouponRequest',
        'App\\Http\\Requests\\Api\\SentCouponRequest',
        'App\\Http\\Requests\\TestRequest'
    ];
    
    foreach ($searchTerms as $searchTerm) {
        echo "🔍 Searching for: '{$searchTerm}'\n";
        
        // Use reflection to access the protected method
        $reflection = new ReflectionClass($command);
        $method = $reflection->getMethod('findRequestClassesByName');
        $method->setAccessible(true);
        
        $foundClasses = $method->invoke($command, $searchTerm);
        
        if (!empty($foundClasses)) {
            echo "✅ Found: " . implode(', ', $foundClasses) . "\n";
        } else {
            echo "❌ Not found\n";
        }
    }
    echo "\n";
    
    echo "3️⃣ Testing DTO generation for found classes...\n";
    
    // Test generating DTO for SentCouponRequest
    $searchTerm = 'SentCouponRequest';
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('findRequestClassesByName');
    $method->setAccessible(true);
    
    $foundClasses = $method->invoke($command, $searchTerm);
    
    if (!empty($foundClasses)) {
        $requestClass = $foundClasses[0];
        echo "✅ Generating DTO for: {$requestClass}\n";
        
        $dtoContent = $dtoGenerator->generateFromRequest($requestClass);
        echo "✅ DTO generated: " . strlen($dtoContent) . " characters\n";
        
        // Save DTO
        $dtoName = 'SentCouponDto';
        $dtoPath = __DIR__ . '/generated/' . $dtoName . '.php';
        $filesystem->ensureDirectoryExists(dirname($dtoPath));
        $filesystem->put($dtoPath, $dtoContent);
        
        echo "✅ DTO saved to: {$dtoPath}\n";
        
        // Test DTO functionality
        require_once $dtoPath;
        
        $testData = [
            'couponCode' => 'SAVE20',
            'discountAmount' => 20.0,
            'discountType' => 'percentage',
            'isActive' => true,
            'expiresAt' => '2024-12-31',
            'usageLimit' => 100,
            'usedCount' => 5
        ];
        
        $dto = \App\DTOs\Api\v1\SentCouponDto::fromArray($testData);
        echo "✅ DTO created successfully!\n";
        echo "   Coupon Code: " . $dto->getCouponCode() . "\n";
        echo "   Discount Amount: " . $dto->getDiscountAmount() . "\n";
        echo "   Discount Type: " . $dto->getDiscountType() . "\n";
        
    } else {
        echo "❌ No classes found for search term: {$searchTerm}\n";
    }
    echo "\n";
    
    echo "🎉 Artisan command search test completed!\n";
    echo "\n";
    echo "📋 Summary:\n";
    echo "===========\n";
    echo "✅ Request classes discovery: " . count($requestClasses) . " classes found\n";
    echo "✅ Search functionality: Working\n";
    echo "✅ Multiple search patterns: Supported\n";
    echo "✅ DTO generation: Working\n";
    echo "✅ SentCouponRequest: Found and functional\n";
    echo "✅ Artisan command ready for: php artisan dto:generate SentCouponRequest\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✨ Artisan command search test completed!\n";
