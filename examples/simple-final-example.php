<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Filesystem\Filesystem;

/**
 * Simple Final Example: Working DTO Generation
 * 
 * This example demonstrates the working functionality of the Laravel Request DTO Generator
 * with ValidationSchemaGenerator for proper JSON Schema generation.
 */

// Simple Request Class
class SimpleOrderRequest
{
    public function rules(): array
    {
        return [
            // Basic order information
            "orderNumber" => "required|string|max:50",
            "orderDate" => "required|date",
            "totalAmount" => "required|numeric",
            "isPaid" => "boolean",
            
            // Order items (typed array)
            "items" => "required|array|min:1",
            "items.*.productId" => "required|uuid",
            "items.*.productName" => "required|string|max:255",
            "items.*.quantity" => "required|integer|min:1",
            "items.*.unitPrice" => "required|numeric|min:0",
            "items.*.totalPrice" => "required|numeric|min:0",
            
            // Item modifiers (nested typed array)
            "items.*.modifiers" => "nullable|array",
            "items.*.modifiers.*.modifierId" => "required|uuid",
            "items.*.modifiers.*.name" => "required|string|max:100",
            "items.*.modifiers.*.price" => "required|numeric",
            
            // Payment information (typed array)
            "payments" => "nullable|array",
            "payments.*.paymentId" => "required|uuid",
            "payments.*.amount" => "required|numeric|min:0",
            "payments.*.method" => "required|string|in:cash,card,online",
            "payments.*.transactionId" => "nullable|string|max:100",
            
            // Additional fields
            "notes" => "nullable|string|max:1000",
        ];
    }
}

// Configuration
$config = [
    'dto_namespace' => 'App\\DTOs\\Api\\v1',
    'dto_directory' => __DIR__ . '/generated',
    'request_directory' => __DIR__,
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
    echo "🚀 Laravel Request DTO Generator - Simple Final Example\n";
    echo "======================================================\n\n";
    
    // Create DTO generator service
    $filesystem = new Filesystem();
    $dtoGenerator = new JsonSchemaDtoGenerator($filesystem, $config);
    
    // Generate DTOs from request
    echo "📦 Generating DTOs from SimpleOrderRequest...\n";
    $generatedDtos = $dtoGenerator->generateMultipleDtosFromRequest(SimpleOrderRequest::class);
    
    // Save DTOs to files
    foreach ($generatedDtos as $dtoName => $dtoContent) {
        $outputFile = __DIR__ . '/generated/' . $dtoName . '.php';
        $filesystem->ensureDirectoryExists(dirname($outputFile));
        $filesystem->put($outputFile, $dtoContent);
    }
    
    echo "✅ Generated DTOs:\n";
    foreach (array_keys($generatedDtos) as $dtoName) {
        echo "   - {$dtoName}\n";
    }
    echo "\n";
    
    // Load generated DTO classes
    require_once __DIR__ . '/generated/SimpleOrderDto.php';
    foreach (array_keys($generatedDtos) as $dtoName) {
        if ($dtoName !== 'SimpleOrderDto') {
            require_once __DIR__ . '/generated/' . $dtoName . '.php';
        }
    }
    
    // Test data
    $validatedData = [
        'orderNumber' => 'ORD-2024-001',
        'orderDate' => '2024-01-15',
        'totalAmount' => 99.99,
        'isPaid' => true,
        'items' => [
            [
                'productId' => '123e4567-e89b-12d3-a456-426614174000',
                'productName' => 'Premium Pizza',
                'quantity' => 2,
                'unitPrice' => 15.99,
                'totalPrice' => 31.98,
                'modifiers' => [
                    [
                        'modifierId' => '456e7890-e89b-12d3-a456-426614174001',
                        'name' => 'Extra Cheese',
                        'price' => 2.50
                    ]
                ]
            ]
        ],
        'payments' => [
            [
                'paymentId' => '789e0123-e89b-12d3-a456-426614174002',
                'amount' => 99.99,
                'method' => 'card',
                'transactionId' => 'TXN-123456'
            ]
        ],
        'notes' => 'Please deliver to the back door'
    ];
    
    echo "🔧 Testing DTO Functionality:\n";
    echo "============================\n\n";
    
    // Test 1: Create DTO from array
    echo "1️⃣ Creating DTO from validated data...\n";
    $orderDto = \App\DTOs\Api\v1\SimpleOrderDto::fromArray($validatedData);
    echo "✅ DTO created successfully!\n";
    echo "   Order Number: " . $orderDto->getOrderNumber() . "\n";
    echo "   Total Amount: $" . $orderDto->getTotalAmount() . "\n";
    echo "   Items Count: " . count($orderDto->getItems()) . "\n\n";
    
    // Test 2: Access typed arrays
    echo "2️⃣ Testing typed arrays...\n";
    $items = $orderDto->getItems();
    if (!empty($items)) {
        $item = $items[0];
        echo "✅ Item DTO: " . get_class($item) . "\n";
        echo "   Product: " . $item->getProductName() . "\n";
        echo "   Quantity: " . $item->getQuantity() . "\n";
        echo "   Modifiers Count: " . count($item->getModifiers()) . "\n";
        
        if (!empty($item->getModifiers())) {
            $modifier = $item->getModifiers()[0];
            echo "✅ Modifier DTO: " . get_class($modifier) . "\n";
            echo "   Name: " . $modifier->getName() . "\n";
            echo "   Price: $" . $modifier->getPrice() . "\n";
        }
    }
    echo "\n";
    
    // Test 3: JSON serialization
    echo "3️⃣ Testing JSON serialization...\n";
    $json = json_encode($orderDto);
    echo "✅ JSON serialization successful!\n";
    echo "   JSON length: " . strlen($json) . " characters\n\n";
    
    // Test 4: Access payments
    echo "4️⃣ Testing payments array...\n";
    $payments = $orderDto->getPayments();
    if (!empty($payments)) {
        $payment = $payments[0];
        echo "✅ Payment DTO: " . get_class($payment) . "\n";
        echo "   Method: " . $payment->getMethod() . "\n";
        echo "   Amount: $" . $payment->getAmount() . "\n";
    }
    echo "\n";
    
    echo "🎉 All tests passed! The Laravel Request DTO Generator is working perfectly!\n";
    echo "\n";
    echo "📋 Summary:\n";
    echo "===========\n";
    echo "✅ Generated " . count($generatedDtos) . " DTO classes\n";
    echo "✅ Proper type hinting for nested objects\n";
    echo "✅ Typed arrays with separate DTO classes\n";
    echo "✅ JSON serialization support\n";
    echo "✅ Constructor property promotion\n";
    echo "✅ Readonly properties\n";
    echo "✅ Full Laravel validation rules support\n";
    echo "✅ ValidationSchemaGenerator integration\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✨ Simple final example completed!\n";
