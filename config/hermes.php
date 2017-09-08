<?php

return [

    'mode' => 'sandbox',
    'locale' => 'en',
    'fallback_locale' => 'en',

    'sandbox' => [
        'base_url' => 'https://jsonplaceholder.typicode.com',
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