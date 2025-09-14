# Laravel Request DTO Generator - Artisan Command Usage

## 🚀 **Artisan команды и сервисы работают правильно!**

### ✅ **Проверенная функциональность:**

- ✅ **Request classes discovery** - поиск Request классов
- ✅ **Single DTO generation** - генерация одного DTO
- ✅ **Multiple DTOs generation** - генерация нескольких DTO
- ✅ **File generation** - сохранение файлов
- ✅ **Generated DTOs functionality** - функциональность сгенерированных DTO
- ✅ **JSON serialization** - JSON сериализация
- ✅ **ValidationSchemaGenerator integration** - интеграция с ValidationSchemaGenerator
- ✅ **Artisan command core functionality** - основная функциональность Artisan команды

## 📋 **Использование Artisan команды:**

### 1. **Генерация DTO для конкретного Request:**
```bash
php artisan dto:generate TestRequest
```

### 2. **Генерация DTO для всех Request классов:**
```bash
php artisan dto:generate --all
```

### 3. **Принудительная перезапись существующих DTO:**
```bash
php artisan dto:generate TestRequest --force
```

### 4. **Генерация с кастомным namespace:**
```bash
php artisan dto:generate TestRequest --namespace="App\\Custom\\DTOs"
```

### 5. **Генерация в кастомную директорию:**
```bash
php artisan dto:generate TestRequest --directory="/path/to/custom/dto/directory"
```

## 🔧 **Конфигурация:**

Файл конфигурации: `config/request-dto-generator.php`

```php
return [
    'dto_namespace' => 'App\\DTOs',
    'dto_directory' => app_path('DTOs'),
    'request_directory' => app_path('Http/Requests'),
    'dto_base_class' => 'BellissimoPizza\\RequestDtoGenerator\\BaseDto',
    'auto_generate_properties' => true,
    'include_validation_rules' => true,
    'generate_constructor' => true,
    'generate_accessors' => true,
    'readonly_properties' => true,
    'constructor_property_promotion' => true,
    'property_visibility' => 'private',
    'generate_separate_dtos_for_arrays' => true,
];
```

## 📝 **Пример Request класса:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
            'isActive' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|uuid',
            'items.*.productName' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.totalPrice' => 'required|numeric|min:0',
        ];
    }
}
```

## 🎯 **Сгенерированные DTO:**

### **TestDto** (основной DTO):
```php
<?php

namespace App\DTOs\Api\v1;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class TestDto extends BaseDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly int $age,
        private readonly bool $isActive,
        private readonly array $items
    ) {}

    // Getters and other methods...
}
```

### **ItemsDto** (DTO для элементов массива):
```php
<?php

namespace App\DTOs\Api\v1;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class ItemsDto extends BaseDto
{
    public function __construct(
        private readonly string $productId,
        private readonly string $productName,
        private readonly int $quantity,
        private readonly int|float $unitPrice,
        private readonly int|float $totalPrice
    ) {}

    // Getters and other methods...
}
```

## 🧪 **Тестирование:**

### **Запуск тестов:**
```bash
# Тест основной функциональности
php examples/simple-final-example.php

# Тест Artisan команды
php examples/test-artisan-direct.php

# Тест с ValidationSchemaGenerator
php examples/test-simple-with-validation-schema.php
```

## 🎉 **Результат:**

**Laravel Request DTO Generator** полностью готов к использованию:

- ✅ **Artisan команды работают**
- ✅ **Сервисы работают**
- ✅ **ValidationSchemaGenerator интегрирован**
- ✅ **Генерация всех DTO работает**
- ✅ **Типизация правильная**
- ✅ **JSON сериализация работает**
- ✅ **Constructor property promotion работает**
- ✅ **Readonly properties работают**

**Пакет готов к продакшену!** 🚀
