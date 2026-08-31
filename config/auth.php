<?php
// Merge these entries into your existing config/auth.php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        // ...existing 'web' and 'api' guards stay as-is...

        // Separate guard for the admin panel, backed by the `admins` table.
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        // ...existing 'users' provider stays as-is...

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        // ...existing entries...
    ],
];
