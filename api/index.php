<?php

// Aktifkan error reporting total
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Gunakan optimasi Vercel untuk storage
    if (isset($_SERVER['VERCEL_URL'])) {
        require __DIR__ . '/../bootstrap/vercel-boot.php';
    }
    
    // Jalankan Laravel normal
    require __DIR__ . '/../public/index.php';
} catch (\Exception $e) {
    echo "<h1>Laravel Crash!</h1>";
    echo "<p>Pesan: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (\Error $e) {
    echo "<h1>Laravel Fatal Error!</h1>";
    echo "<p>Pesan: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
