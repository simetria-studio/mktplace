<?php

return [
    'name' => 'Biguaçu',
    'manifest' => [
        'name' => env('APP_NAME', 'Biguaçu'),
        'short_name' => 'BIGUAÇU',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#3D550C',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> '#3D550C',
        'icons' => [
            '72x72' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/site/imgs/logo.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/site/imgs/logo.png',
            '750x1334' => '/site/imgs/logo.png',
            '828x1792' => '/site/imgs/logo.png',
            '1125x2436' => '/site/imgs/logo.png',
            '1242x2208' => '/site/imgs/logo.png',
            '1242x2688' => '/site/imgs/logo.png',
            '1536x2048' => '/site/imgs/logo.png',
            '1668x2224' => '/site/imgs/logo.png',
            '1668x2388' => '/site/imgs/logo.png',
            '2048x2732' => '/site/imgs/logo.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Comprador',
                'description' => 'Perfil Comprador',
                'url' => '/perfil'
            ],
            [
                'name' => 'Vendedor',
                'description' => 'Perfil Vendedor',
                'url' => '/vendedor'
            ]
        ],
        'custom' => []
    ]
];
