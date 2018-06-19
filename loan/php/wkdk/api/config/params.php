<?php
return [
    'adminEmail' => 'admin@example.com',
    'error_log' => [
        'enabled' => true,
        'ignores' => [
            [
                'method' => 'GET',
                'path' => '/favicon.ico',
                'exception' => '*',
            ],
        ],
    ],
    'timeout_log' => [
        'enabled' => true,
        'configs' => [
            [
                'method' => 'PUT',
                'path' => '/1/my-icon',
                'timeout' => 30000,
            ],
            [
                'method' => '*',
                'path' => '*',
                'timeout' => 1000,
            ],
        ],
    ],
    'custom_log' => [
        'enabled' => true,
    ],
    'log_analyze' => [
        'enabled' => true,
        'percent' => 0.1,
    ],
];
