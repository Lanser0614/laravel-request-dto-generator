<?php

namespace BellissimoPizza\RequestDtoGenerator\Commands;

use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateDtoFromRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dto:generate 
                            {request? : The Request class name to generate DTO from (e.g., CreateUserRequest)}
                            {--all : Generate DTOs for all Request classes}
                            {--force : Overwrite existing DTO files}
                            {--namespace= : Custom namespace for generated DTOs}
                            {--directory= : Custom directory for generated DTOs}';

    /**
     * The console command description.
     */
    protected $description = 'Generate DTO classes from Laravel Request files';

    protected JsonSchemaDtoGenerator $dtoGenerator;

    public function __construct()
    {
        parent::__construct();
        
        $this->dtoGenerator = new JsonSchemaDtoGenerator(
            new Filesystem(),
            config('request-dto-generator', [])
        );
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $request = $this->argument('request');
        $all = $this->option('all');
        $force = $this->option('force');
        $namespace = $this->option('namespace');
        $directory = $this->option('directory');

        // Update config if custom options provided
        if ($namespace) {
            $this->dtoGenerator = new JsonSchemaDtoGenerator(
                new Filesystem(),
                array_merge(config('request-dto-generator', []), ['dto_namespace' => $namespace])
            );
        }

        if ($directory) {
            $this->dtoGenerator = new JsonSchemaDtoGenerator(
                new Filesystem(),
                array_merge(config('request-dto-generator', []), ['dto_directory' => $directory])
            );
        }

        if ($all) {
            return $this->generateAllDtos($force);
        }

        if ($request) {
            return $this->generateSingleDto($request, $force);
        }

        $this->error('Please specify a Request class or use --all option');
        return 1;
    }

    /**
     * Generate DTO for a single Request class
     */
    protected function generateSingleDto(string $requestName, bool $force): int
    {
        try {
            // Find Request classes by name
            $requestClasses = $this->findRequestClassesByName($requestName);
            
            if (empty($requestClasses)) {
                $this->error("No Request classes found with name '{$requestName}'");
                return 1;
            }
            
            // If multiple classes found, let user choose
            if (count($requestClasses) > 1) {
                $requestClass = $this->selectRequestClass($requestClasses);
                if (!$requestClass) {
                    $this->info('Generation cancelled');
                    return 0;
                }
            } else {
                $requestClass = $requestClasses[0];
            }

            // Check if Request class has rules method
            $reflection = new \ReflectionClass($requestClass);
            if (!$reflection->hasMethod('rules')) {
                $this->error("Request class '{$requestClass}' does not have a rules() method");
                return 1;
            }

            $dtoName = $this->getDtoName($requestClass);
            $dtoPath = config('request-dto-generator.dto_directory') . '/' . $dtoName . '.php';

            // Check if DTO already exists
            if (file_exists($dtoPath) && !$force) {
                if (!$this->confirm("DTO '{$dtoName}' already exists. Overwrite?")) {
                    $this->info('Generation cancelled');
                    return 0;
                }
            }

            $this->info("Generating DTO for Request class: {$requestClass}");
            
            $dtoContent = $this->dtoGenerator->generateFromRequest($requestClass);
            $dtoPath = config('request-dto-generator.dto_directory') . '/' . $dtoName . '.php';
            $success = file_put_contents($dtoPath, $dtoContent) !== false;

            if ($success) {
                $this->info("✅ DTO '{$dtoName}' generated successfully at: {$dtoPath}");
                return 0;
            } else {
                $this->error("❌ Failed to generate DTO '{$dtoName}'");
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("Error generating DTO: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Generate DTOs for all Request classes
     */
    protected function generateAllDtos(bool $force): int
    {
        $this->info('Scanning for Request classes...');
        
        $requestClasses = $this->dtoGenerator->getRequestClasses();
        
        if (empty($requestClasses)) {
            $this->warn('No Request classes found in: ' . config('request-dto-generator.request_directory'));
            return 0;
        }

        $this->info("Found " . count($requestClasses) . " Request classes");

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($requestClasses as $requestClass) {
            try {
                $dtoName = $this->getDtoName($requestClass);
                $dtoPath = config('request-dto-generator.dto_directory') . '/' . $dtoName . '.php';

                // Skip if DTO exists and not forcing
                if (file_exists($dtoPath) && !$force) {
                    $this->line("⏭️  Skipping {$dtoName} (already exists)");
                    $skipped++;
                    continue;
                }

                $this->line("🔄 Generating DTO for: {$requestClass}");
                
                $dtoContent = $this->dtoGenerator->generateFromRequest($requestClass);
                $dtoPath = config('request-dto-generator.dto_directory') . '/' . $dtoName . '.php';
                $success = file_put_contents($dtoPath, $dtoContent) !== false;

                if ($success) {
                    $this->line("✅ Generated: {$dtoName}");
                    $generated++;
                } else {
                    $this->line("❌ Failed: {$dtoName}");
                    $errors++;
                }

            } catch (\Exception $e) {
                $this->line("❌ Error with {$requestClass}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Generation Summary:");
        $this->line("✅ Generated: {$generated}");
        $this->line("⏭️  Skipped: {$skipped}");
        $this->line("❌ Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Find Request classes by name
     */
    protected function findRequestClassesByName(string $className): array
    {
        $foundClasses = [];
        
        // Get all Request classes
        $allRequestClasses = $this->dtoGenerator->getRequestClasses();
        
        foreach ($allRequestClasses as $requestClass) {
            $baseName = class_basename($requestClass);
            
            // Check if class name matches (with or without "Request" suffix)
            if ($baseName === $className || 
                $baseName === $className . 'Request' ||
                (str_ends_with($className, 'Request') && $baseName === $className)) {
                $foundClasses[] = $requestClass;
            }
        }
        
        return $foundClasses;
    }

    /**
     * Let user select from multiple Request classes
     */
    protected function selectRequestClass(array $requestClasses): ?string
    {
        $this->info("Found multiple Request classes with the same name:");
        $this->newLine();
        
        $choices = [];
        foreach ($requestClasses as $index => $requestClass) {
            $choices[] = $requestClass;
            $this->line(($index + 1) . ". {$requestClass}");
        }
        
        $this->newLine();
        $choice = $this->ask("Please select which class to use (1-" . count($requestClasses) . ")", 1);
        
        if (is_numeric($choice) && $choice >= 1 && $choice <= count($requestClasses)) {
            return $choices[$choice - 1];
        }
        
        return null;
    }

    /**
     * Get DTO name from Request class name
     */
    protected function getDtoName(string $requestClass): string
    {
        $className = class_basename($requestClass);
        
        // Remove "Request" suffix if present
        if (str_ends_with($className, 'Request')) {
            $className = substr($className, 0, -7);
        }
        
        return $className . 'Dto';
    }
}
