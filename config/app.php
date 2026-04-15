<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Help Desk'),
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim((string) env('APP_URL', ''), '/'),
    'session_name' => env('SESSION_NAME', 'helpdesk_session'),
    'session_secure' => filter_var(env('SESSION_SECURE', false), FILTER_VALIDATE_BOOLEAN),
    'upload_dir' => env('UPLOAD_DIR', 'storage/uploads'),
    'max_upload_mb' => (int) env('MAX_UPLOAD_MB', 10),
];

