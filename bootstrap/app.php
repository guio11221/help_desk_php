<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/bootstrap/helpers.php';
require_once BASE_PATH . '/bootstrap/autoload.php';

load_env(BASE_PATH . '/.env');

$config = require BASE_PATH . '/config/app.php';

date_default_timezone_set('America/Cuiaba');

if (!headers_sent()) {
    session_name($config['session_name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $config['session_secure'],
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

