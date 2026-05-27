<?php

// Setup /tmp storage untuk Vercel (filesystem read-only)
foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app',
] as $path) {
    if (!is_dir($path)) @mkdir($path, 0755, true);
}

// Buat .env dari environment variables Vercel jika tidak ada
if (!file_exists(__DIR__ . '/../.env')) {
    $keys = [
        'APP_NAME','APP_ENV','APP_KEY','APP_DEBUG','APP_URL',
        'APP_LOCALE','APP_FALLBACK_LOCALE','APP_FAKER_LOCALE',
        'APP_MAINTENANCE_DRIVER','APP_TIMEZONE','BCRYPT_ROUNDS',
        'LOG_CHANNEL','LOG_STACK','LOG_LEVEL',
        'DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD',
        'SESSION_DRIVER','SESSION_LIFETIME','SESSION_ENCRYPT','SESSION_PATH',
        'BROADCAST_CONNECTION','FILESYSTEM_DISK','QUEUE_CONNECTION','CACHE_STORE',
        'MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_USERNAME','MAIL_PASSWORD',
        'MAIL_ENCRYPTION','MAIL_FROM_ADDRESS','MAIL_FROM_NAME',
        'TELEGRAM_BOT_TOKEN',
        'ZERO_TRUST_ENABLED','ZERO_TRUST_DEVICE_FINGERPRINTING','ZERO_TRUST_MFA_ENABLED','ZERO_TRUST_CONTEXT_AWARE',
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

// Paksa response JSON jika terjadi error saat boot (menghindari error 'view not found')
$_SERVER['HTTP_ACCEPT'] = 'application/json';

// Jalankan Laravel dan tangkap error mentah
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');

    // Telusuri ke penyebab asli (root cause)
    $root = $e;
    while ($root->getPrevious()) $root = $root->getPrevious();

    echo "=== ROOT CAUSE (Penyebab Asli) ===\n";
    echo get_class($root) . ": " . $root->getMessage() . "\n";
    echo "File: " . $root->getFile() . ":" . $root->getLine() . "\n\n";

    echo "=== EXCEPTION CHAIN ===\n";
    $cur = $e; $i = 1;
    while ($cur) {
        echo "[$i] " . get_class($cur) . ": " . $cur->getMessage() . "\n";
        echo "     at " . $cur->getFile() . ":" . $cur->getLine() . "\n";
        $cur = $cur->getPrevious(); $i++;
    }
}
