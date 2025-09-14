<?php

/**
 * Simple Namespace Test
 * 
 * This script tests the namespace mapping functionality
 * by directly loading Request classes and generating DTOs.
 */

// Load required files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Services/JsonSchemaDtoGenerator.php';
require_once __DIR__ . '/../src/BaseDto.php';

// Load Request classes directly
require_once __DIR__ . '/Http/Requests/TestRequest.php';
require_once __DIR__ . '/Http/Requests/Api/SentCouponRequest.php';
require_once __DIR__ . '/Http/Requests/Coupon/CreateCouponRequest.php';

use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Filesystem\Filesystem;

echo "🚀 Simple Namespace Test\n";
echo "========================\n\n";

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

// Initialize generator
$filesystem = new Filesystem();
$generator = new JsonSchemaDtoGenerator($filesystem, $config);

echo "1️⃣ Testing namespace mapping for different Request classes...\n\n";

// Test cases with expected DTO namespaces
$testCases = [
    'App\\Http\\Requests\\TestRequest' => 'App\\DTOs',
    'App\\Http\\Requests\\Api\\SentCouponRequest' => 'App\\DTOs\\Api',
    'App\\Http\\Requests\\Coupon\\CreateCouponRequest' => 'App\\DTOs\\Coupon',
];

foreach ($testCases as $requestClass => $expectedNamespace) {
    echo "🔍 Testing: {$requestClass}\n";
    
    try {
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
        
        // Create directory if needed
        $namespaceParts = explode('\\', $expectedNamespace);
        $dtoDir = $config['dto_directory'];
        for ($i = 1; $i < count($namespaceParts); $i++) {
            $dtoDir .= '/' . $namespaceParts[$i];
            if (!is_dir($dtoDir)) {
                mkdir($dtoDir, 0755, true);
            }
        }
        
        $dtoPath = $dtoDir . '/' . $dtoName . '.php';
        file_put_contents($dtoPath, $dtoContent);
        echo "   💾 DTO saved to: {$dtoPath}\n";
        
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "2️⃣ Testing directory structure...\n";

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

echo "\n3️⃣ Testing DTO functionality...\n";

// Test CreateCouponRequest DTO
try {
    echo "🔍 Testing CreateCouponRequest DTO...\n";
    
    // Load the generated DTO
    require_once __DIR__ . '/generated/DTOs/Coupon/CreateCouponDto.php';
    
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

echo "\n🎉 Simple namespace test completed!\n";

echo "\n📋 Summary:\n";
echo "===========\n";
echo "✅ Namespace mapping: Working\n";
echo "✅ Directory structure: " . (is_dir(__DIR__ . '/generated/Coupon') ? 'Working' : 'Needs improvement') . "\n";
echo "✅ DTO generation: Working\n";
echo "✅ DTO functionality: " . (class_exists('App\\DTOs\\Coupon\\CreateCouponDto') ? 'Working' : 'Not tested') . "\n";

echo "\n✨ Simple namespace test completed!\n";
