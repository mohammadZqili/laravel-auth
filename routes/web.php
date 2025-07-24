<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel API Service',
        'version' => '1.0.0',
        'environment' => app()->environment(),
        'timestamp' => now()->toISOString(),
    ]);
});

Route::get('/healthz', function () {
    $health = [
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'service' => 'api',
        'version' => '1.0.0',
        'checks' => []
    ];

    // Database connectivity check
    try {
        DB::connection()->getPdo();
        $health['checks']['database'] = 'healthy';
    } catch (\Exception $e) {
        $health['checks']['database'] = 'unhealthy';
        $health['status'] = 'unhealthy';
    }

    // Cache connectivity check
    try {
        Cache::put('health_check', 'test', 10);
        $test = Cache::get('health_check');
        $health['checks']['cache'] = $test === 'test' ? 'healthy' : 'unhealthy';
    } catch (\Exception $e) {
        $health['checks']['cache'] = 'unhealthy';
        $health['status'] = 'unhealthy';
    }

    // Memory usage check
    $memoryUsage = memory_get_usage(true);
    $memoryLimit = ini_get('memory_limit');
    $memoryLimitBytes = $memoryLimit === '-1' ? PHP_INT_MAX : str_replace('M', '', $memoryLimit) * 1024 * 1024;
    $memoryPercent = ($memoryUsage / $memoryLimitBytes) * 100;
    
    $health['checks']['memory'] = [
        'status' => $memoryPercent < 90 ? 'healthy' : 'warning',
        'usage_bytes' => $memoryUsage,
        'usage_percent' => round($memoryPercent, 2)
    ];

    $statusCode = $health['status'] === 'healthy' ? 200 : 503;
    
    return response()->json($health, $statusCode);
});

Route::get('/ready', function () {
    // Readiness probe - checks if service is ready to accept traffic
    try {
        // Check if migrations are up to date
        \Artisan::call('migrate:status');
        
        return response()->json([
            'status' => 'ready',
            'timestamp' => now()->toISOString(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'not ready',
            'error' => 'Database migrations pending',
            'timestamp' => now()->toISOString(),
        ], 503);
    }
});

Route::get('/metrics', function () {
    // Basic metrics endpoint for monitoring
    return response()->json([
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true),
        'uptime' => \Cache::get('app_start_time', now()),
        'requests_total' => \Cache::increment('requests_total', 1),
        'timestamp' => now()->toISOString(),
    ]);
}); 