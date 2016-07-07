<?php

return [
    'prefix' => 'admin',
    'filter' => [
        'auth' => [
            OroCMS\Admin\Middleware\Authenticate::class,
            OroCMS\Admin\Middleware\AdminOnly::class
        ],
        'guest' => OroCMS\Admin\Middleware\RedirectIfAuthenticated::class,
    ],
    'models' => [
        'user' => OroCMS\Admin\Entities\User::class,
        'role' => OroCMS\Admin\Entities\Role::class
    ],
    'views' => [
        'layout' => 'admin::layouts.master',
    ],
    'modules' => [
        'path' => base_path('modules'),
        'migration' => [
            'path' => 'Database/Migrations'
        ],
        'lang' => [
            'path' => 'Resources/lang',
            'default_locale' => 'en'
        ]
    ],
    'plugins' => [
        'path' => base_path('plugins')
    ],
    'themes' => [
        'path' => base_path('resources/views/themes'),
        'default_theme' => 'default',
        'cp' => [
            'default_theme' => 'bootstrapped'
        ]
    ]
];
