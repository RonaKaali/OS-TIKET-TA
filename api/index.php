<?php

// Forward Vercel requests to normal index.php
if (isset($_SERVER['VERCEL_URL'])) {
    require __DIR__ . '/../bootstrap/vercel-boot.php';
}
require __DIR__ . '/../public/index.php';

