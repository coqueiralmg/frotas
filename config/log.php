<?php

use Cake\Log\Engine\FileLog;

return [
    /**
     * Configures logging options
     */
    'Log' => [
        'debug' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'debug',
            'url' => env('LOG_DEBUG_URL', null),
            'scopes' => false,
            'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'error',
            'url' => env('LOG_ERROR_URL', null),
            'scopes' => false,
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
        'queries' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'queries',
            'url' => env('LOG_QUERIES_URL', null),
            'scopes' => ['queriesLog'],
        ],
        'mail' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'mail',
            'scopes' => 'mail',
            'levels' => ['notice'],
            'url' => env('LOG_ERROR_URL', null),
        ],
        'register' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'access',
            'scopes' => 'register',
            'levels' => ['info'],
            'url' => env('LOG_ERROR_URL', null),
        ]
    ],
];
