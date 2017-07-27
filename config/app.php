<?php

return [
    'services' => [
        Cart\ServiceProviders\DatabaseServiceProviders::class
    ],
    'drivers' => [
        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ]
];
