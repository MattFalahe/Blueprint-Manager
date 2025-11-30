<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blueprint Manager Configuration
    |--------------------------------------------------------------------------
    */

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

    // Activity IDs for industry jobs
    'industry_activities' => [
        'copying' => 3,
        'me_research' => 4,
        'te_research' => 5,
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
