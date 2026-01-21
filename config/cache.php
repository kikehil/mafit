<?php

use Illuminate\Support\Str;

return [
    'default' => env('CACHE_STORE', 'database'),
    'stores' => [
        'database' => [
            'driver' => 'database',
            'table' => env('CACHE_DATABASE_TABLE', 'cache'),
            'connection' => env('CACHE_DATABASE_CONNECTION'),
            'lock_connection' => env('CACHE_DATABASE_CONNECTION'),
        ],
    ],
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];











