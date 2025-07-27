<?php
return [
    'name' => env('APP_NAME', 'Laravel Auth API'),
    'env' => env('APP_ENV', 'local'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    ],
    'aliases' => [
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
    ],
];
