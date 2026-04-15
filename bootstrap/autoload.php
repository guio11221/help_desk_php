<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $map = [
        BASE_PATH . '/src/Controllers/' . $class . '.php',
        BASE_PATH . '/src/Core/' . $class . '.php',
        BASE_PATH . '/src/Http/' . $class . '.php',
        BASE_PATH . '/src/Repositories/' . $class . '.php',
        BASE_PATH . '/src/Services/' . $class . '.php',
        BASE_PATH . '/src/Support/' . $class . '.php',
        BASE_PATH . '/src/Database/' . $class . '.php',
    ];

    foreach ($map as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

