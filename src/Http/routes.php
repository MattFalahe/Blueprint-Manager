<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => '\BlueprintManager\Http\Controllers',
    'middleware' => ['web', 'auth', 'locale'],
    'prefix' => 'blueprint-manager',
], function () {
    
    // Blueprint Library Routes
    Route::get('/', [
        'as' => 'blueprint-manager.library',
        'uses' => 'BlueprintLibraryController@index',
        'middleware' => 'can:blueprint-manager.view',
    ]);
    
    Route::get('/library/data/{corporation_id}', [
        'as' => 'blueprint-manager.library.data',
        'uses' => 'BlueprintLibraryController@getBlueprintsData',
        'middleware' => 'can:blueprint-manager.view',
    ]);
    
    Route::get('/library/categories/{corporation_id}', [
        'as' => 'blueprint-manager.library.categories',
        'uses' => 'BlueprintLibraryController@getCategories',
        'middleware' => 'can:blueprint-manager.view',
    ]);

    Route::get('/library/details/{corporation_id}/{type_id}', [
        'as' => 'blueprint-manager.library.details',
        'uses' => 'BlueprintLibraryController@getBlueprintDetails',
        'middleware' => 'can:blueprint-manager.view',
    ]);

    // Blueprint Request Routes
    Route::get('/requests', [
        'as' => 'blueprint-manager.requests',
        'uses' => 'BlueprintRequestController@index',
        'middleware' => 'can:blueprint-manager.request',
    ]);
    
    Route::post('/requests/create', [
        'as' => 'blueprint-manager.requests.create',
        'uses' => 'BlueprintRequestController@store',
        'middleware' => 'can:blueprint-manager.request',
    ]);
    
    Route::get('/requests/data', [
        'as' => 'blueprint-manager.requests.data',
        'uses' => 'BlueprintRequestController@getRequestsData',
        'middleware' => 'can:blueprint-manager.request',
    ]);

    Route::get('/requests/blueprints/{corporation_id}', [
        'as' => 'blueprint-manager.requests.blueprints',
        'uses' => 'BlueprintRequestController@getAvailableBlueprints',
        'middleware' => 'can:blueprint-manager.request',
    ]);

    // Delete request (users can delete their own pending/rejected requests)
    Route::delete('/requests/{blueprintRequest}', [
        'as' => 'blueprint-manager.requests.delete',
        'uses' => 'BlueprintRequestController@destroy',
        'middleware' => 'can:blueprint-manager.request',
    ]);

    // Request Management Routes (for managers)
    Route::post('/requests/{blueprintRequest}/approve', [
        'as' => 'blueprint-manager.requests.approve',
        'uses' => 'BlueprintRequestController@approve',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::post('/requests/{blueprintRequest}/reject', [
        'as' => 'blueprint-manager.requests.reject',
        'uses' => 'BlueprintRequestController@reject',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::post('/requests/{blueprintRequest}/fulfill', [
        'as' => 'blueprint-manager.requests.fulfill',
        'uses' => 'BlueprintRequestController@fulfill',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);

    // Statistics Routes
    Route::get('/statistics', [
        'as' => 'blueprint-manager.statistics',
        'uses' => 'BlueprintStatisticsController@index',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::get('/statistics/overall', [
        'as' => 'blueprint-manager.statistics.overall',
        'uses' => 'BlueprintStatisticsController@getOverallStats',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::get('/statistics/characters', [
        'as' => 'blueprint-manager.statistics.characters',
        'uses' => 'BlueprintStatisticsController@getCharacterStats',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::get('/statistics/blueprints', [
        'as' => 'blueprint-manager.statistics.blueprints',
        'uses' => 'BlueprintStatisticsController@getBlueprintStats',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::get('/statistics/character/{characterId}', [
        'as' => 'blueprint-manager.statistics.character-details',
        'uses' => 'BlueprintStatisticsController@getCharacterDetails',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::get('/statistics/timeseries/{days?}', [
        'as' => 'blueprint-manager.statistics.timeseries',
        'uses' => 'BlueprintStatisticsController@getTimeSeriesStats',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);
    
    Route::get('/statistics/corporation/{corporationId}', [
        'as' => 'blueprint-manager.statistics.corporation',
        'uses' => 'BlueprintStatisticsController@getCorporationStats',
        'middleware' => 'can:blueprint-manager.manage_requests',
    ]);

    // Settings Routes
    Route::get('/settings', [
        'as' => 'blueprint-manager.settings',
        'uses' => 'BlueprintSettingsController@index',
        'middleware' => 'can:blueprint-manager.settings',
    ]);
    
    Route::post('/settings/container-config', [
        'as' => 'blueprint-manager.settings.container.store',
        'uses' => 'BlueprintSettingsController@storeContainerConfig',
        'middleware' => 'can:blueprint-manager.settings',
    ]);
    
    Route::put('/settings/container-config/{config}', [
        'as' => 'blueprint-manager.settings.container.update',
        'uses' => 'BlueprintSettingsController@updateContainerConfig',
        'middleware' => 'can:blueprint-manager.settings',
    ]);
    
    Route::delete('/settings/container-config/{config}', [
        'as' => 'blueprint-manager.settings.container.delete',
        'uses' => 'BlueprintSettingsController@deleteContainerConfig',
        'middleware' => 'can:blueprint-manager.settings',
    ]);
    
    Route::get('/settings/detect-containers/{corporation_id}', [
        'as' => 'blueprint-manager.settings.detect',
        'uses' => 'BlueprintSettingsController@detectContainers',
        'middleware' => 'can:blueprint-manager.settings',
    ]);

    // Detection Settings Routes
    Route::get('/settings/detection-settings/{corporationId}', [
        'uses' => 'BlueprintSettingsController@getDetectionSettings',
        'as' => 'blueprint-manager.settings.detection-settings',
        'middleware' => ['web', 'auth'],
    ]);
    
    Route::post('/settings/detection-settings/{corporationId}', [
        'uses' => 'BlueprintSettingsController@saveDetectionSettings',
        'as' => 'blueprint-manager.settings.save-detection-settings',
        'middleware' => ['web', 'auth'],
    ]);

    // Webhook Configuration Routes
    Route::get('/settings/webhooks', [
        'as' => 'blueprint-manager.settings.webhooks',
        'uses' => 'BlueprintSettingsController@getWebhookConfigs',
        'middleware' => 'can:blueprint-manager.settings',
    ]);

    Route::post('/settings/webhooks', [
        'as' => 'blueprint-manager.settings.webhooks.store',
        'uses' => 'BlueprintSettingsController@storeWebhookConfig',
        'middleware' => 'can:blueprint-manager.settings',
    ]);

    Route::put('/settings/webhooks/{id}', [
        'as' => 'blueprint-manager.settings.webhooks.update',
        'uses' => 'BlueprintSettingsController@updateWebhookConfig',
        'middleware' => 'can:blueprint-manager.settings',
    ]);

    Route::delete('/settings/webhooks/{id}', [
        'as' => 'blueprint-manager.settings.webhooks.delete',
        'uses' => 'BlueprintSettingsController@deleteWebhookConfig',
        'middleware' => 'can:blueprint-manager.settings',
    ]);

    Route::post('/settings/webhooks/test', [
        'as' => 'blueprint-manager.settings.webhooks.test',
        'uses' => 'BlueprintSettingsController@testWebhook',
        'middleware' => 'can:blueprint-manager.settings',
    ]);

    // Help & Documentation Route
    Route::get('/help', [
        'as' => 'blueprint-manager.help',
        'uses' => 'BlueprintHelpController@index',
        'middleware' => 'can:blueprint-manager.view',
    ]);
});
