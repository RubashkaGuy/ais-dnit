<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::redirect('/', '/admin');

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::get('/__diag', function () {
    $payload = [
        'php_version' => PHP_VERSION,
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_key_set' => ! empty(config('app.key')),
        'app_url' => config('app.url'),
        'session_driver' => config('session.driver'),
        'cache_store' => config('cache.default'),
        'db_connection' => config('database.default'),
        'db_host' => config('database.connections.'.config('database.default').'.host'),
        'db_database' => config('database.connections.'.config('database.default').'.database'),
        'loaded_extensions' => [
            'intl' => extension_loaded('intl'),
            'gd' => extension_loaded('gd'),
            'zip' => extension_loaded('zip'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
        ],
    ];

    try {
        DB::connection()->getPdo();
        $payload['db_connect'] = 'ok';
        $payload['tables_exist'] = [
            'users' => Schema::hasTable('users'),
            'sessions' => Schema::hasTable('sessions'),
            'cache' => Schema::hasTable('cache'),
            'migrations' => Schema::hasTable('migrations'),
        ];
    } catch (\Throwable $e) {
        $payload['db_connect'] = 'FAILED: '.$e->getMessage();
    }

    return response()->json($payload, 200, [], JSON_PRETTY_PRINT)
        ->header('Content-Type', 'application/json');
})->withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class]);
