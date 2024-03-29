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
        'reCaptcha' => [
            'urlVerify' => 'https://www.google.com/recaptcha/api/siteverify',
            'default' => [
                'siteKey' => '6LeLIjUUAAAAACJAPVBLYecOJhY1tNA2g1uFQ63m',
                'secretKey' => '6LeLIjUUAAAAAAR5Q18vvucNk8oM7qCaFhfu57qZ'
            ],
            'invisible' => [
                'siteKey' => '6LfbJjUUAAAAAP7GFrCMqGaz8BaexqXmYha6ozbV',
                'secretKey' => '6LfbJjUUAAAAAKaq5HkD-9nqZNvlVHJz7or9E3eC'
            ]
        ],
        'login' => [
            'attemps' => [
                'max' => 5,
                'warning' => 3
            ],
            'access' => 'restrict'
        ]
    ],
];
