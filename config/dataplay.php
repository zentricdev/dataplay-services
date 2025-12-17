<?php

return [
    'services' => [
        'querylog' => [
            'enabled' => env('DATAPLAY_QUERYLOG_ENABLED', false),
            'path' => env('DATAPLAY_QUERYLOG_PATH', storage_path('logs')),
        ],
    ],
];
