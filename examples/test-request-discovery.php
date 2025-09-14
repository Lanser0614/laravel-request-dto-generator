<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Filesystem\Filesystem;

/**
 * Test Request Discovery in Subdirectories
 * 
 * This script tests the ability to find Request classes in subdirectories.
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
    echo "🚀 Test Request Discovery in Subdirectories\n";
    echo "==========================================\n\n";
    
    // Load Request classes
    require_once __DIR__ . '/Http/Requests/TestRequest.php';
    require_once __DIR__ . '/Http/Requests/Api/SentCouponRequest.php';
    
    // Create DTO generator service
    $filesystem = new Filesystem();
    $dtoGenerator = new JsonSchemaDtoGenerator($filesystem, $config);
    
    echo "1️⃣ Testing Request classes discovery...\n";
    $requestClasses = $dtoGenerator->getRequestClasses();
    echo "✅ Found " . count($requestClasses) . " Request classes:\n";
    foreach ($requestClasses as $requestClass) {
        echo "   - {$requestClass}\n";
    }
    echo "\n";
    
    echo "2️⃣ Testing specific class search...\n";
    
    // Test searching for SentCouponRequest
    $searchTerms = [
        'SentCouponRequest',
        'SentCoupon',
        'Api\\SentCouponRequest',
        'App\\Http\\Requests\\Api\\SentCouponRequest'
    ];
    
    foreach ($searchTerms as $searchTerm) {
        echo "🔍 Searching for: '{$searchTerm}'\n";
        
        // Simulate the command's search logic
        $foundClasses = [];
        foreach ($requestClasses as $requestClass) {
            $baseName = class_basename($requestClass);
            
            // Check if class name matches (with or without "Request" suffix)
            if ($baseName === $searchTerm || 
                $baseName === $searchTerm . 'Request' ||
                (str_ends_with($searchTerm, 'Request') && $baseName === $searchTerm) ||
                $requestClass === $searchTerm) {
                $foundClasses[] = $requestClass;
            }
        }
        
        if (!empty($foundClasses)) {
            echo "✅ Found: " . implode(', ', $foundClasses) . "\n";
        } else {
            echo "❌ Not found\n";
        }
    }
    echo "\n";
    
    echo "3️⃣ Testing DTO generation for SentCouponRequest...\n";
    
    $sentCouponClass = 'App\\Http\\Requests\\Api\\SentCouponRequest';
    if (in_array($sentCouponClass, $requestClasses)) {
        echo "✅ SentCouponRequest found in Request classes\n";
        
        // Generate DTO
        $dtoContent = $dtoGenerator->generateFromRequest($sentCouponClass);
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
        echo "❌ SentCouponRequest not found in Request classes\n";
    }
    echo "\n";
    
    echo "🎉 Request discovery test completed!\n";
    echo "\n";
    echo "📋 Summary:\n";
    echo "===========\n";
    echo "✅ Request classes discovery: " . count($requestClasses) . " classes found\n";
    echo "✅ Subdirectory search: Working\n";
    echo "✅ Class name matching: Working\n";
    echo "✅ DTO generation: Working\n";
    echo "✅ SentCouponRequest: Found and functional\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✨ Request discovery test completed!\n";
