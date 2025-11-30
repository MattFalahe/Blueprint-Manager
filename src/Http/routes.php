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
});
