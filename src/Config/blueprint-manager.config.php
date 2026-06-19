<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blueprint Manager Configuration
    |--------------------------------------------------------------------------
    */

    // Plugin version (fallback for the Version Status card when Composer
    // metadata is unavailable, e.g. running from a dev branch).
    'version' => '2.0.0',

    // Default categories for blueprint display
    'default_categories' => [
        'BPO' => 'Original Blueprints',
        'BPC' => 'Blueprint Copies',
    ],

    // Default request status transitions
    'request_statuses' => [
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'fulfilled' => 'Fulfilled',
        'rejected' => 'Rejected',
    ],

    // Activity IDs for industry jobs (per EVE ESI)
    'industry_activities' => [
        'te_research' => 3,
        'me_research' => 4,
        'copying' => 5,
    ],

    // Default pagination limits
    'pagination' => [
        'blueprints' => 50,
        'requests' => 25,
    ],

    // Cache settings
    'cache' => [
        'enabled' => true,
        'ttl' => 300, // 5 minutes
    ],
];
