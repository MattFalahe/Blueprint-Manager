<?php

return [
    'blueprint-manager' => [
        'name'          => 'Blueprint Manager',
        'label'         => 'blueprint-manager::menu.main_level',
        'plural'        => true,
        'icon'          => 'fas fa-clipboard-list',
        'route_segment' => 'blueprint-manager',
        'permission'    => 'blueprint-manager.view',
        'entries'       => [
            [
                'name'  => 'Blueprint Library',
                'label' => 'blueprint-manager::menu.library',
                'icon'  => 'fas fa-book',
                'route' => 'blueprint-manager.library',
                'permission' => 'blueprint-manager.view',
            ],
            [
                'name'  => 'Requests',
                'label' => 'blueprint-manager::menu.requests',
                'icon'  => 'fas fa-inbox',
                'route' => 'blueprint-manager.requests',
                'permission' => 'blueprint-manager.request',
            ],
            [
                'name'  => 'Statistics',
                'label' => 'blueprint-manager::menu.statistics',
                'icon'  => 'fas fa-chart-line',
                'route' => 'blueprint-manager.statistics',
                'permission' => 'blueprint-manager.manage_requests',
            ],
            [
                'name'  => 'Settings',
                'label' => 'blueprint-manager::menu.settings',
                'icon'  => 'fas fa-cog',
                'route' => 'blueprint-manager.settings',
                'permission' => 'blueprint-manager.settings',
            ],
            [
                'name'  => 'Help & Documentation',
                'label' => 'blueprint-manager::menu.help',
                'icon'  => 'fas fa-question-circle',
                'route' => 'blueprint-manager.help',
                'permission' => 'blueprint-manager.view',
            ],
        ]
    ]
];
