<?php

/*
|--------------------------------------------------------------------------
| Vercel Optimization
|--------------------------------------------------------------------------
*/

if (isset($_SERVER['VERCEL_URL'])) {
    // Force storage to /tmp for Vercel
    $app->useStoragePath('/tmp/storage');
    
    // Ensure directories exist
    if (!is_dir('/tmp/storage/framework/views')) {
        mkdir('/tmp/storage/framework/views', 0755, true);
    }
}
