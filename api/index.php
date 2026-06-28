<?php

// Setup /tmp storage untuk Vercel (filesystem read-only)
foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/logs',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
] as $path) {
    if (!is_dir($path)) @mkdir($path, 0755, true);
}

// Redirect cache path ke /tmp (karena Vercel filesystem read-only di luar /tmp)
$cachePath = '/tmp/storage/framework/cache';
putenv("APP_SERVICES_CACHE={$cachePath}/services.php");
putenv("APP_PACKAGES_CACHE={$cachePath}/packages.php");
putenv("APP_CONFIG_CACHE={$cachePath}/config.php");
putenv("APP_ROUTES_CACHE={$cachePath}/routes.php");
putenv("APP_EVENTS_CACHE={$cachePath}/events.php");
putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");
$_SERVER['APP_SERVICES_CACHE'] = "{$cachePath}/services.php";
$_SERVER['APP_PACKAGES_CACHE'] = "{$cachePath}/packages.php";
$_SERVER['APP_CONFIG_CACHE'] = "{$cachePath}/config.php";
$_SERVER['APP_ROUTES_CACHE'] = "{$cachePath}/routes.php";
$_SERVER['APP_EVENTS_CACHE'] = "{$cachePath}/events.php";
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// =========================================================================
// FORCE OVERRIDE: Selalu paksa env vars dari Vercel menimpa .env yang ada
// Ini diperlukan karena .env di-commit dengan setting MySQL lokal,
// sedangkan Vercel menggunakan Supabase PostgreSQL.
// =========================================================================
$criticalKeys = [
    // Database - WAJIB dari Vercel env vars
    'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_URL',
    // App settings
    'APP_KEY', 'APP_URL', 'APP_ENV', 'APP_DEBUG',
    // Cache & Queue - paksa ke 'array'/'sync' agar tidak butuh DB saat boot
    'CACHE_STORE', 'QUEUE_CONNECTION',
    // Session
    'SESSION_DRIVER', 'SESSION_LIFETIME',
    // Mail
    'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD',
    'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME',
    // Telegram
    'TELEGRAM_BOT_TOKEN',
    // Zero Trust
    'ZERO_TRUST_ENABLED', 'VPN_BLOCK_ENABLED', 'ZERO_TRUST_DEVICE_FINGERPRINTING', 'ZERO_TRUST_MFA_ENABLED', 'ZERO_TRUST_CONTEXT_AWARE',
];

foreach ($criticalKeys as $key) {
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        // Override di $_ENV, $_SERVER, dan putenv agar Laravel membacanya
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv("{$key}={$value}");
    }
}

// Jika CACHE_STORE belum di-set dari Vercel, paksa ke 'file' (butuh persistent storage)
// JANGAN paksa ke 'array' karena RateLimiter dan VPN cache butuh persist antar request
if (empty(getenv('CACHE_STORE')) || getenv('CACHE_STORE') === 'database' || getenv('CACHE_STORE') === 'array') {
    $cs = getenv('CACHE_STORE');
    // Hanya paksa ke file jika Vercel belum set CACHE_STORE yang valid
    if ($cs === false || $cs === '' || $cs === 'database' || $cs === 'array') {
        putenv('CACHE_STORE=file');
        $_ENV['CACHE_STORE'] = 'file';
        $_SERVER['CACHE_STORE'] = 'file';
    }
}

// Session: database driver butuh tabel sessions (belum ada di project).
// Paksa cookie agar session persisten di serverless Vercel tanpa DB tambahan.
$sessionDriver = getenv('SESSION_DRIVER');
if ($sessionDriver === false || $sessionDriver === '' || $sessionDriver === 'file' || $sessionDriver === 'database') {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
}

// Jika QUEUE_CONNECTION belum di-set, paksa ke 'sync' (tidak butuh DB)
if (empty(getenv('QUEUE_CONNECTION')) || getenv('QUEUE_CONNECTION') === 'database') {
    $qc = getenv('QUEUE_CONNECTION');
    if ($qc === false || $qc === '' || $qc === 'database') {
        putenv('QUEUE_CONNECTION=sync');
        $_ENV['QUEUE_CONNECTION'] = 'sync';
        $_SERVER['QUEUE_CONNECTION'] = 'sync';
    }
}

// Buat .env dari environment variables Vercel jika tidak ada
if (!file_exists(__DIR__ . '/../.env')) {
    $keys = [
        'APP_NAME','APP_ENV','APP_KEY','APP_DEBUG','APP_URL',
        'APP_LOCALE','APP_FALLBACK_LOCALE','APP_FAKER_LOCALE',
        'APP_MAINTENANCE_DRIVER','APP_TIMEZONE','BCRYPT_ROUNDS',
        'LOG_CHANNEL','LOG_STACK','LOG_LEVEL',
        'DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD','DB_URL',
        'SESSION_DRIVER','SESSION_LIFETIME','SESSION_ENCRYPT','SESSION_PATH',
        'BROADCAST_CONNECTION','FILESYSTEM_DISK','QUEUE_CONNECTION','CACHE_STORE',
        'MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_USERNAME','MAIL_PASSWORD',
        'MAIL_ENCRYPTION','MAIL_FROM_ADDRESS','MAIL_FROM_NAME',
        'TELEGRAM_BOT_TOKEN',
        'ZERO_TRUST_ENABLED','VPN_BLOCK_ENABLED','ZERO_TRUST_DEVICE_FINGERPRINTING','ZERO_TRUST_MFA_ENABLED','ZERO_TRUST_CONTEXT_AWARE',
        'DEVICE_TRUST_SCORE_THRESHOLD','DEVICE_TRUST_SESSION_DURATION',
        'MFA_TOTP_ENABLED','MFA_EMAIL_ENABLED','MFA_BACKUP_CODES_COUNT',
        'RATE_LIMIT_REQUESTS_PER_MINUTE','RATE_LIMIT_REQUESTS_PER_HOUR','RATE_LIMIT_ENABLE_ADAPTIVE',
        'GEO_LOCATION_ENABLED','ALLOWED_COUNTRIES',
        'SESSION_VALIDATION_INTERVAL','TOKEN_ROTATION_INTERVAL',
        'RISK_SCORE_THRESHOLD_HIGH','RISK_SCORE_THRESHOLD_CRITICAL',
        'LOG_SECURITY_DAYS','VIEW_COMPILED_PATH',
    ];
    $env = '';
    foreach ($keys as $k) {
        $v = getenv($k);
        if ($v !== false && $v !== '') {
            if (preg_match('/\s|"/', $v)) $v = '"' . addcslashes($v, '"') . '"';
            $env .= "$k=$v\n";
        }
    }
    if ($env) @file_put_contents(__DIR__ . '/../.env', $env);
}

// Jalankan Laravel dan tangkap error mentah
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');

    // Telusuri ke penyebab asli (root cause)
    $root = $e;
    while ($root->getPrevious()) $root = $root->getPrevious();

    echo "=== EXCEPTION CHAIN ===\n";
    $cur = $e; $i = 1;
    while ($cur && $i <= 3) {
        echo "[$i] " . get_class($cur) . ": " . $cur->getMessage() . "\n";
        echo "     at " . $cur->getFile() . ":" . $cur->getLine() . "\n";
        $cur = $cur->getPrevious(); $i++;
    }

    echo "\n\n=== ROOT CAUSE (PENYEBAB ASLI) ===\n";
    echo get_class($root) . ": " . $root->getMessage() . "\n";
    echo "File: " . $root->getFile() . ":" . $root->getLine() . "\n\n";
}
