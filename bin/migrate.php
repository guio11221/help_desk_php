<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$args = $argv;
array_shift($args);

$command = $args[0] ?? 'up';
$manager = new MigrationManager(new Database(config('database')), BASE_PATH . '/database/migrations');

try {
    switch ($command) {
        case 'up':
            $applied = $manager->runPending();
            foreach ($applied as $name) {
                fwrite(STDOUT, 'Migrated: ' . $name . PHP_EOL);
            }
            if (!$applied) {
                fwrite(STDOUT, "No pending migrations.\n");
            }
            break;

        case 'rollback':
            $rolledBack = $manager->rollbackLastBatch();
            foreach ($rolledBack as $name) {
                fwrite(STDOUT, 'Rolled back: ' . $name . PHP_EOL);
            }
            if (!$rolledBack) {
                fwrite(STDOUT, "No batches to rollback.\n");
            }
            break;

        case 'status':
            $applied = $manager->applied();
            $pending = $manager->pending();

            fwrite(STDOUT, "Applied migrations:\n");
            foreach ($applied as $row) {
                fwrite(STDOUT, sprintf("- %s [batch %d]\n", $row['migration'], $row['batch']));
            }

            fwrite(STDOUT, "Pending migrations:\n");
            foreach ($pending as $name => $file) {
                fwrite(STDOUT, "- {$name}\n");
            }
            break;

        default:
            fwrite(STDERR, "Usage: php bin/migrate.php [up|rollback|status]\n");
            exit(1);
    }
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}

