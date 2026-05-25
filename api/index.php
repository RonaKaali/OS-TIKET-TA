<?php

// Tampilkan semua error secara RAW untuk debugging Vercel
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Setup /tmp storage untuk Vercel (filesystem read-only)
$tmpPaths = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app',
    '/tmp/bootstrap/cache',
];
foreach ($tmpPaths as $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

// Buat .env dari environment variables Vercel jika tidak ada
if (!file_exists(__DIR__ . '/../.env')) {
    $envVars = [
        'APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL',
        'APP_LOCALE', 'APP_FALLBACK_LOCALE', 'APP_FAKER_LOCALE',
        'APP_MAINTENANCE_DRIVER', 'APP_TIMEZONE', 'BCRYPT_ROUNDS',
        'LOG_CHANNEL', 'LOG_STACK', 'LOG_LEVEL',
        'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
        'SESSION_DRIVER', 'SESSION_LIFETIME', 'SESSION_ENCRYPT', 'SESSION_PATH',
        'BROADCAST_CONNECTION', 'FILESYSTEM_DISK', 'QUEUE_CONNECTION', 'CACHE_STORE',
        'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD',
        'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME',
        'TELEGRAM_BOT_TOKEN',
        'ZERO_TRUST_ENABLED', 'ZERO_TRUST_DEVICE_FINGERPRINTING', 'ZERO_TRUST_MFA_ENABLED', 'ZERO_TRUST_CONTEXT_AWARE',
        'DEVICE_TRUST_SCORE_THRESHOLD', 'DEVICE_TRUST_SESSION_DURATION',
        'MFA_TOTP_ENABLED', 'MFA_EMAIL_ENABLED', 'MFA_BACKUP_CODES_COUNT',
        'RATE_LIMIT_REQUESTS_PER_MINUTE', 'RATE_LIMIT_REQUESTS_PER_HOUR', 'RATE_LIMIT_ENABLE_ADAPTIVE',
        'GEO_LOCATION_ENABLED', 'ALLOWED_COUNTRIES',
        'SESSION_VALIDATION_INTERVAL', 'TOKEN_ROTATION_INTERVAL',
        'RISK_SCORE_THRESHOLD_HIGH', 'RISK_SCORE_THRESHOLD_CRITICAL',
        'LOG_SECURITY_DAYS', 'VITE_APP_NAME',
        'VIEW_COMPILED_PATH', 'CACHE_DIRECTORY',
    ];

    $envContent = '';
    foreach ($envVars as $key) {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            // Bungkus dengan quote jika ada spasi
            if (strpos($value, ' ') !== false || strpos($value, '"') !== false) {
                $value = '"' . str_replace('"', '\\"', $value) . '"';
            }
            $envContent .= "{$key}={$value}\n";
        }
    }

    if (!empty($envContent)) {
        file_put_contents('/tmp/.env', $envContent);
        // Buat symlink atau copy ke root
        @copy('/tmp/.env', __DIR__ . '/../.env');
    }
}

require __DIR__ . '/../public/index.php';

