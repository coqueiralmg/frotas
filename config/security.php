<?php

return [
    /**
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', 'c7dc482249a36e0107dfba84d16f875ac1d14d91a1f29dd93fd2602036dc66f9'),
        'login' => [
            'attemps' => [
                'max' => 5,
                'warning' => 3
            ],
            'access' => 'public'
        ]
    ],
];
