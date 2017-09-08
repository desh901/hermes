<?php

return [

    'mode' => 'sandbox',
    'locale' => 'en',
    'fallback_locale' => 'en',

    'sandbox' => [
        'test' => 'lalla',
        'base_url' => 'http://localhost:1234',
        'timeout' => 20,


        'credentials' => [
            'type' => 'basic',
            'username' => 'lallo',
            'password' => 'lalletto'
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