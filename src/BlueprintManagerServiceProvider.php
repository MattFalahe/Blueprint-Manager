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

        // Optional Manager Core integration (capabilities for cross-plugin
        // reads). class_exists-guarded so Blueprint Manager runs standalone.
        $this->registerPluginBridgeCapabilities();
    }

    /**
     * Expose read-only blueprint-request stats to other plugins (HR Manager)
     * via the Manager Core PluginBridge. No-op when Manager Core isn't
     * installed, so the plugin is fully standalone.
     */
    private function registerPluginBridgeCapabilities()
    {
        if (!class_exists('ManagerCore\Services\PluginBridge')) {
            return;
        }

        try {
            $bridge = app(\ManagerCore\Services\PluginBridge::class);

            // blueprint.getCharacterStats($characterId, $corporationId)
            //   -> per-member request engagement (counts by status, rejection
            //      rate, favourite types). Consumers call once per character
            //      (e.g. per alt) and aggregate.
            $bridge->registerCapability('blueprint-manager', 'blueprint.getCharacterStats',
                fn ($characterId, $corporationId) => app(\BlueprintManager\Services\BlueprintBridgeService::class)
                    ->getCharacterStats((int) $characterId, (int) $corporationId)
            );

            // blueprint.getCorpSummary($corporationId)
            //   -> corp-wide rollup: totals by status, unique requesters,
            //      pending-backlog age, top requesters.
            $bridge->registerCapability('blueprint-manager', 'blueprint.getCorpSummary',
                fn ($corporationId) => app(\BlueprintManager\Services\BlueprintBridgeService::class)
                    ->getCorpSummary((int) $corporationId)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('[Blueprint Manager] Could not register bridge capabilities: ' . $e->getMessage());
        }
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
