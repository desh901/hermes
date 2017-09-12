<?php

return [

    'mode' => 'sandbox',
    'locale' => 'en',
    'fallback_locale' => 'en',

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

    ]

];