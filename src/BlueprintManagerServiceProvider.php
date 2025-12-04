<?php

namespace BlueprintManager;

use Seat\Services\AbstractSeatPlugin;

class BlueprintManagerServiceProvider extends AbstractSeatPlugin
{
    public function boot()
    {
        // Check if routes are cached before loading
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
        
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/', 'blueprint-manager');
        $this->loadViewsFrom(__DIR__ . '/resources/views/', 'blueprint-manager');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations/');

        // Add publications
        $this->add_publications();
    }

    /**
     * Add content which must be published.
     */
    private function add_publications()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/Config/blueprint-manager.config.php' => config_path('blueprint-manager.php'),
        ], ['config', 'seat']);
        
        // Publish assets
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('vendor/blueprint-manager'),
        ], ['public', 'seat']);
    }

    public function register()
    {
        // Register sidebar configuration
        $this->mergeConfigFrom(__DIR__ . '/Config/Menu/package.sidebar.php', 'package.sidebar');
        
        // Register permissions
        $this->registerPermissions(__DIR__ . '/Config/Permissions/blueprint-manager.permissions.php', 'blueprint-manager');
        
        // Register config
        $this->mergeConfigFrom(__DIR__.'/Config/blueprint-manager.config.php', 'blueprint-manager');
    }

    public function getName(): string
    {
        return 'Blueprint Manager';
    }

    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/MattFalahe/blueprint-manager';
    }

    public function getPackagistPackageName(): string
    {
        return 'blueprint-manager';
    }

    public function getPackagistVendorName(): string
    {
        return 'mattfalahe';
    }
}
