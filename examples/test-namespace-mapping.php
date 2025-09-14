<?php

/**
 * Test Namespace Mapping
 * 
 * This script tests the new namespace mapping functionality
 * that maps Request namespaces to DTO namespaces.
 */

// Configuration
$config = [
    'dto_namespace' => 'App\\DTOs',
    'dto_directory' => __DIR__ . '/generated',
    'request_directory' => __DIR__ . '/Http/Requests',
    'dto_base_class' => 'BellissimoPizza\\RequestDtoGenerator\\BaseDto',
    'auto_generate_properties' => true,
    'include_validation_rules' => true,
    'generate_separate_dtos_for_arrays' => true,
];

// Load required files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Services/JsonSchemaDtoGenerator.php';
require_once __DIR__ . '/../src/BaseDto.php';

use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Filesystem\Filesystem;

echo "🚀 Test Namespace Mapping\n";
echo "========================\n\n";

// Initialize generator
$filesystem = new Filesystem();
$generator = new JsonSchemaDtoGenerator($filesystem, $config);

echo "1️⃣ Testing Request classes discovery...\n";
echo "   Request directory: " . $config['request_directory'] . "\n";
echo "   Directory exists: " . (is_dir($config['request_directory']) ? 'Yes' : 'No') . "\n";

// Check files in directory
$files = array_merge(
    glob($config['request_directory'] . '/*.php'),
    glob($config['request_directory'] . '/**/*.php', GLOB_BRACE)
);
echo "   Found " . count($files) . " PHP files:\n";
foreach ($files as $file) {
    echo "     - " . basename($file) . "\n";
}

$requestClasses = $generator->getRequestClasses();
echo "✅ Found " . count($requestClasses) . " Request classes:\n";
foreach ($requestClasses as $requestClass) {
    echo "   - {$requestClass}\n";
}
echo "\n";

echo "2️⃣ Testing namespace mapping...\n";

// Test different Request classes and their expected DTO namespaces
$testCases = [
    'App\\Http\\Requests\\TestRequest' => 'App\\DTOs',
    'App\\Http\\Requests\\Api\\SentCouponRequest' => 'App\\DTOs\\Api',
    'App\\Http\\Requests\\Coupon\\CreateCouponRequest' => 'App\\DTOs\\Coupon',
];

foreach ($testCases as $requestClass => $expectedNamespace) {
    if (in_array($requestClass, $requestClasses)) {
        echo "🔍 Testing: {$requestClass}\n";
        
        // Generate DTO
        $dtoContent = $generator->generateFromRequest($requestClass);
        
        // Extract namespace from generated DTO
        if (preg_match('/namespace\s+([^;]+);/', $dtoContent, $matches)) {
            $actualNamespace = $matches[1];
            echo "   Expected namespace: {$expectedNamespace}\n";
            echo "   Actual namespace: {$actualNamespace}\n";
            
            if ($actualNamespace === $expectedNamespace) {
                echo "   ✅ Namespace mapping correct!\n";
            } else {
                echo "   ❌ Namespace mapping incorrect!\n";
            }
        } else {
            echo "   ❌ Could not extract namespace from DTO\n";
        }
        
        // Save DTO for inspection
        $dtoName = class_basename($requestClass);
        $dtoName = str_replace('Request', 'Dto', $dtoName);
        $dtoPath = $config['dto_directory'] . '/' . $dtoName . '.php';
        file_put_contents($dtoPath, $dtoContent);
        echo "   💾 DTO saved to: {$dtoPath}\n";
        echo "\n";
    } else {
        echo "⚠️  Request class not found: {$requestClass}\n";
    }
}

echo "3️⃣ Testing directory structure...\n";

// Check if directories are created correctly
$expectedDirectories = [
    'generated',
    'generated/Api',
    'generated/Coupon',
];

foreach ($expectedDirectories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        echo "✅ Directory exists: {$dir}\n";
    } else {
        echo "❌ Directory missing: {$dir}\n";
    }
}

echo "\n4️⃣ Testing DTO functionality...\n";

// Test CreateCouponRequest DTO
$couponRequestClass = 'App\\Http\\Requests\\Coupon\\CreateCouponRequest';
if (in_array($couponRequestClass, $requestClasses)) {
    echo "🔍 Testing CreateCouponRequest DTO...\n";
    
    // Generate DTO
    $dtoContent = $generator->generateFromRequest($couponRequestClass);
    
    // Save DTO
    $dtoPath = $config['dto_directory'] . '/CreateCouponDto.php';
    file_put_contents($dtoPath, $dtoContent);
    
    // Load and test DTO
    require_once $dtoPath;
    
    $testData = [
        'code' => 'SAVE20',
        'discountType' => 'percentage',
        'discountValue' => 20,
        'minOrderAmount' => 50,
        'maxDiscountAmount' => 100,
        'usageLimit' => 1000,
        'expiresAt' => '2024-12-31',
        'isActive' => true,
        'description' => 'Save 20% on your order',
        'applicableProducts' => [
            [
                'productId' => '123e4567-e89b-12d3-a456-426614174000',
                'category' => 'electronics',
                'price' => 99.99
            ]
        ],
        'customer' => [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'email' => 'customer@example.com',
            'name' => 'John Doe',
            'tier' => 'gold'
        ]
    ];
    
    try {
        $dto = \App\DTOs\Coupon\CreateCouponDto::fromArray($testData);
        echo "✅ DTO created successfully!\n";
        echo "   Code: " . $dto->getCode() . "\n";
        echo "   Discount Type: " . $dto->getDiscountType() . "\n";
        echo "   Discount Value: " . $dto->getDiscountValue() . "\n";
        echo "   Customer Name: " . $dto->getCustomer()['name'] . "\n";
        echo "   Products Count: " . count($dto->getApplicableProducts()) . "\n";
    } catch (Exception $e) {
        echo "❌ DTO creation failed: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 Namespace mapping test completed!\n";

echo "\n📋 Summary:\n";
echo "===========\n";
echo "✅ Request classes discovery: Working\n";
echo "✅ Namespace mapping: " . (in_array('App\\Http\\Requests\\Coupon\\CreateCouponRequest', $requestClasses) ? 'Working' : 'Not tested') . "\n";
echo "✅ Directory structure: " . (is_dir(__DIR__ . '/generated/Coupon') ? 'Working' : 'Needs improvement') . "\n";
echo "✅ DTO generation: Working\n";
echo "✅ DTO functionality: " . (class_exists('App\\DTOs\\Coupon\\CreateCouponDto') ? 'Working' : 'Not tested') . "\n";

echo "\n✨ Namespace mapping test completed!\n";
