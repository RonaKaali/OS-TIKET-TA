<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Forward Vercel requests to normal index.php
if (isset($_SERVER['VERCEL_URL'])) {
    require __DIR__ . '/../bootstrap/vercel-boot.php';
}
require __DIR__ . '/../public/index.php';

