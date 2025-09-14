<?php

namespace BellissimoPizza\RequestDtoGenerator;

use BellissimoPizza\RequestDtoGenerator\Commands\GenerateDtoFromRequestCommand;
use BellissimoPizza\RequestDtoGenerator\Services\JsonSchemaDtoGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class RequestDtoGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/request-dto-generator.php',
            'request-dto-generator'
        );

        // Register DTO Generator Service
        $this->app->singleton(JsonSchemaDtoGenerator::class, function ($app) {
            return new JsonSchemaDtoGenerator(
                $app->make(Filesystem::class),
                config('request-dto-generator', [])
            );
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/request-dto-generator.php' => config_path('request-dto-generator.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs/request-dto-generator'),
            ], 'stubs');

            $this->commands([
                GenerateDtoFromRequestCommand::class,
            ]);
        }
    }
}
