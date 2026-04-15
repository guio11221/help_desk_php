<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$router = new Router();
require BASE_PATH . '/routes/web.php';

verify_csrf();
$router->dispatch(request_method(), request_path());

