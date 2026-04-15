<?php

declare(strict_types=1);

function load_env(string $file): void
{
    if (!is_file($file)) {
        return;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($value === false || $value === null || $value === '') ? $default : $value;
}

function config(string $path, mixed $default = null): mixed
{
    static $items = null;

    if ($items === null) {
        $items = [
            'app' => require BASE_PATH . '/config/app.php',
            'database' => require BASE_PATH . '/config/database.php',
        ];
    }

    $segments = explode('.', $path);
    $value = $items;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function base_url(string $path = ''): string
{
    $base = rtrim((string) config('app.url', ''), '/');
    $path = ltrim($path, '/');
    return $path === '' ? $base : $base . '/' . $path;
}

function asset(string $path): string
{
    return base_url(ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function request_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    if ($scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
        $path = substr($path, strlen($scriptDir));
    }

    $path = '/' . trim($path, '/');
    return $path === '//' ? '/' : $path;
}

function path_is(string $path): bool
{
    return request_path() === $path;
}

function path_starts_with(string $path): bool
{
    return str_starts_with(request_path(), $path);
}

function is_post(): bool
{
    return request_method() === 'POST';
}

function flash(string $key, ?string $message = null): mixed
{
    if ($message === null) {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    $_SESSION['_flash'][$key] = $message;
    return null;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function preserve_old_input(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old_input(): void
{
    unset($_SESSION['_old']);
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if (!is_post()) {
        return;
    }

    $token = $_POST['_token'] ?? '';
    if (!$token || !hash_equals((string) ($_SESSION['_csrf'] ?? ''), (string) $token)) {
        http_response_code(419);
        exit('CSRF token invalido.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function app_theme(): string
{
    $theme = $_SESSION['theme'] ?? 'business';
    $allowed = ['business', 'graphite', 'forest'];
    return in_array($theme, $allowed, true) ? $theme : 'business';
}

function avatar_url(?array $user): ?string
{
    if (!$user || empty($user['id']) || empty($user['avatar_path'])) {
        return null;
    }

    $stamp = urlencode((string) ($user['updated_at'] ?? ''));
    return base_url('/media/avatar?user=' . (int) $user['id'] . '&v=' . $stamp);
}

function app_notifications(): array
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $user = current_user();
    if (!$user) {
        return $cache = ['count' => 0, 'items' => []];
    }

    $repo = new TicketRepository(new Database(config('database')));

    return $cache = [
        'count' => $repo->notificationCount($user),
        'items' => $repo->notificationFeed($user, 5),
    ];
}

function auth_check(): bool
{
    return current_user() !== null;
}

function auth_role(?string $role = null): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }

    if ($role === null) {
        return true;
    }

    return $user['role'] === $role || $user['role'] === 'admin';
}

function require_auth(): void
{
    if (!auth_check()) {
        flash('error', 'Faça login para continuar.');
        redirect('/login');
    }
}

function require_role(array $roles): void
{
    $user = current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}
