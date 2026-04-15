<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$args = $argv;
array_shift($args);

$options = [];
foreach ($args as $arg) {
    if (str_starts_with($arg, '--') && str_contains($arg, '=')) {
        [$key, $value] = explode('=', substr($arg, 2), 2);
        $options[$key] = trim($value, "\"'");
    }
}

$name = $options['name'] ?? null;
$email = $options['email'] ?? null;
$password = $options['password'] ?? null;
$role = $options['role'] ?? 'admin';

if (!$name || !$email || !$password) {
    fwrite(STDERR, "Uso: php bin/create_admin.php --name=\"Admin\" --email=\"admin@local.test\" --password=\"SenhaForte123!\" --role=admin\n");
    exit(1);
}

if (!Validator::email($email)) {
    fwrite(STDERR, "E-mail invalido.\n");
    exit(1);
}

$repo = new UserRepository(new Database(config('database')));
if ($repo->findByEmail($email)) {
    fwrite(STDOUT, "Usuario ja existe.\n");
    exit(0);
}

$id = $repo->create([
    'name' => $name,
    'email' => strtolower($email),
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'role' => $role,
    'active' => true,
]);

fwrite(STDOUT, 'Administrador criado com sucesso. ID: ' . $id . PHP_EOL);

