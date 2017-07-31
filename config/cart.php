<?php

return [
    'services' => [
        Cart\ServiceProviders\DatabaseServiceProviders::class,
        \Cart\ServiceProviders\RedisServiceProvider::class,
    ],
    'drivers' => [
        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'test',
            'username'  => 'test',
            'password'  => 'test',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ],
        'redis' => [
            'prefix' => 'cart',
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]
    ],
    'storage' => \Cart\Drivers\DatabaseDriver::class
];
