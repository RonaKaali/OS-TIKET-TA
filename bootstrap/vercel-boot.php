<?php

/*
|--------------------------------------------------------------------------
| Vercel Optimization
|--------------------------------------------------------------------------
*/

if (isset($_SERVER['VERCEL_URL'])) {
    // Clear potentially broken caches from local
    if (file_exists('/var/task/user/bootstrap/cache/config.php')) {
        @unlink('/var/task/user/bootstrap/cache/config.php');
    }

    // Force storage to /tmp for Vercel

    $app->useStoragePath('/tmp/storage');
    
    // Ensure directories exist
    $paths = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs'
    ];
    
    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}

