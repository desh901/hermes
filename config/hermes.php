<?php

return [

    'log' => 'single',
    'log_level' => 'debug',
    'log_max_files' => 5,

    'locale' => 'en',
    'fallback_locale' => 'en',

    'mode' => 'sandbox',

    'sandbox' => [
        'base_url' => 'http://www.mocky.io/v2',
        'timeout' => 20,


        'credentials' => [
            'type' => 'basic',
            'username' => 'lallo',
            'password' => 'lalletto'
        ],

        'callbacks' => [
            'verify' => true
        ]
    ],

    'credentials' => [

        'default' => 'basic',

        'types' => [
            'basic',
            'jwt',
            'client'
        ]

    ],

    'providers' => [
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \Hermes\Providers\TranslationServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,
        \Illuminate\Cache\CacheServiceProvider::class,
        \Hermes\Providers\ContextServiceProvider::class,
        \Hermes\Providers\CredentialsServiceProvider::class,
        \Hermes\Providers\HttpBodyParserServiceProvider::class,
        \Hermes\Providers\ConsoleSupportServiceProvider::class,
        \Hermes\Test\ActionServiceProvider::class

    ]

];