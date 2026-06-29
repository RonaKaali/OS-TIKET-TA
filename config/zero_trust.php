<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Zero Trust Security Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Zero Trust Security implementation
    |
    */

    'enabled' => env('ZERO_TRUST_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | VPN / Proxy Detection
    |--------------------------------------------------------------------------
    | Saat diaktifkan, login dari VPN/proxy/datacenter akan ditolak dan
    | dicatat sebagai anomali di Live Security Monitoring.
    |
    */

    'vpn_block_enabled' => env('VPN_BLOCK_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Device Fingerprinting
    |--------------------------------------------------------------------------
    */

    'device_fingerprinting' => env('ZERO_TRUST_DEVICE_FINGERPRINTING', true),
    'device_trust_score_threshold' => env('DEVICE_TRUST_SCORE_THRESHOLD', 70),
    'device_trust_session_duration' => env('DEVICE_TRUST_SESSION_DURATION', 1440), // minutes

    /*
    |--------------------------------------------------------------------------
    | Multi-Factor Authentication (MFA)
    |--------------------------------------------------------------------------
    */

    'mfa_enabled' => env('ZERO_TRUST_MFA_ENABLED', true),
    'mfa_totp_enabled' => env('MFA_TOTP_ENABLED', true),
    'mfa_email_enabled' => env('MFA_EMAIL_ENABLED', true),
    'mfa_backup_codes_count' => env('MFA_BACKUP_CODES_COUNT', 10),

    /*
    |--------------------------------------------------------------------------
    | Context-Aware Access Control
    |--------------------------------------------------------------------------
    */

    'context_aware' => env('ZERO_TRUST_CONTEXT_AWARE', true),
    'geo_location_enabled' => env('GEO_LOCATION_ENABLED', false),
    // Path ke database MaxMind (GeoLite2/GeoIP2) mmdb, contoh:
    // storage/app/geoip/GeoLite2-City.mmdb
    'geoip_db_path' => env('GEOIP_DB_PATH', storage_path('app/geoip/GeoLite2-City.mmdb')),
    'allowed_countries' => explode(',', env('ALLOWED_COUNTRIES', 'ID')),
    'blocked_ips' => explode(',', env('BLOCKED_IPS', '')),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit_requests_per_minute' => env('RATE_LIMIT_REQUESTS_PER_MINUTE', 60),
    'rate_limit_requests_per_hour' => env('RATE_LIMIT_REQUESTS_PER_HOUR', 1000),
    'rate_limit_enable_adaptive' => env('RATE_LIMIT_ENABLE_ADAPTIVE', true),

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */

    'session_validation_interval' => env('SESSION_VALIDATION_INTERVAL', 30), // seconds
    'token_rotation_interval' => env('TOKEN_ROTATION_INTERVAL', 300), // seconds

    /*
    |--------------------------------------------------------------------------
    | Risk Scoring
    |--------------------------------------------------------------------------
    */

    'risk_score_threshold_high' => env('RISK_SCORE_THRESHOLD_HIGH', 70),
    'risk_score_threshold_critical' => env('RISK_SCORE_THRESHOLD_CRITICAL', 85),

    /*
    |--------------------------------------------------------------------------
    | Login Working Hours
    |--------------------------------------------------------------------------
    |
    | ISO-8601 day numbers are used: Monday = 1, Sunday = 7. The end time is
    | exclusive, so a default value of 17:00 rejects attempts at 17:00 onward.
    |
    */

    'working_hours' => [
        'enabled' => env('WORKING_HOURS_LOGIN_BLOCK_ENABLED', true),
        'start' => env('WORKING_HOURS_START', '08:00'),
        'end' => env('WORKING_HOURS_END', '17:00'),
        'days' => explode(',', env('WORKING_HOURS_DAYS', '1,2,3,4,5')),
        'timezone' => env('WORKING_HOURS_TIMEZONE', 'Asia/Makassar'),
    ],

];
