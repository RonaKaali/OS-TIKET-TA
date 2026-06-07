<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Fix untuk Vercel: Setup storage path SEBELUM app boot
if (isset($_SERVER['VERCEL']) || isset($_SERVER['VERCEL_URL']) || getenv('VERCEL')) {
    $tmpPaths = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/storage/app',
    ];
    foreach ($tmpPaths as $path) {
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckUserActivity::class,
            \App\Http\Middleware\RequireMfaVerification::class,
            \App\Http\Middleware\ZeroTrustVerification::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'chatbot/message',
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            error_log('REAL ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        });
    })->create();

// Set storage path setelah app dibuat
if (isset($_SERVER['VERCEL']) || isset($_SERVER['VERCEL_URL']) || getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
