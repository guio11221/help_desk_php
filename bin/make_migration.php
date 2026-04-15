<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$args = $argv;
array_shift($args);

$name = $args[0] ?? '';
if ($name === '') {
    fwrite(STDERR, "Usage: php bin/make_migration.php create_users_table\n");
    exit(1);
}

$mode = $args[1] ?? '--diff';

if ($mode === '--blank') {
    $timestamp = date('Y_m_d_His');
    $fileName = $timestamp . '_' . $name . '.php';
    $path = BASE_PATH . '/database/migrations/' . $fileName;

    $template = <<<'PHP'
<?php

declare(strict_types=1);

return new class(new Database(config('database'))) extends Migration {
    public function up(): void
    {
        // TODO: implement migration
    }

    public function down(): void
    {
        // TODO: implement rollback
    }
};
PHP;

    if (file_put_contents($path, $template . PHP_EOL) === false) {
        fwrite(STDERR, "Failed to create migration.\n");
        exit(1);
    }

    fwrite(STDOUT, 'Created blank migration: database/migrations/' . $fileName . PHP_EOL);
    exit(0);
}

$generator = new SchemaMigrationGenerator(
    BASE_PATH . '/database/schema.php',
    BASE_PATH . '/database/schema.snapshot.json',
    BASE_PATH . '/database/migrations'
);

try {
    $result = $generator->generate($name);
    if (!$result['created']) {
        fwrite(STDOUT, "No schema changes detected.\n");
        exit(0);
    }

    fwrite(STDOUT, 'Created migration: database/migrations/' . $result['file'] . PHP_EOL);
    fwrite(STDOUT, "Updated schema snapshot.\n");
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
