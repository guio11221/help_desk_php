<?php

declare(strict_types=1);

final class MigrationManager
{
    public function __construct(private Database $db, private string $path)
    {
    }

    public function ensureMigrationsTable(): void
    {
        $this->db->execute('
            CREATE TABLE IF NOT EXISTS migrations (
                id BIGSERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                batch INT NOT NULL,
                ran_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ');
    }

    public function pending(): array
    {
        $this->ensureMigrationsTable();

        $ran = $this->db->fetchAll('SELECT migration FROM migrations ORDER BY migration ASC');
        $map = [];
        foreach ($ran as $row) {
            $map[$row['migration']] = true;
        }

        $files = glob($this->path . '/*.php') ?: [];
        sort($files, SORT_STRING);

        $pending = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (!isset($map[$name])) {
                $pending[$name] = $file;
            }
        }

        return $pending;
    }

    public function applied(): array
    {
        $this->ensureMigrationsTable();
        return $this->db->fetchAll('SELECT migration, batch, ran_at FROM migrations ORDER BY batch ASC, migration ASC');
    }

    public function runPending(): array
    {
        $batch = $this->nextBatch();
        $applied = [];

        foreach ($this->pending() as $name => $file) {
            $migration = require $file;
            if (!$migration instanceof Migration) {
                throw new RuntimeException('Invalid migration: ' . $name);
            }

            $this->db->pdo()->beginTransaction();
            try {
                $migration->up();
                $this->db->execute(
                    'INSERT INTO migrations (migration, batch, ran_at) VALUES (:migration, :batch, NOW())',
                    ['migration' => $name, 'batch' => $batch]
                );
                $this->db->pdo()->commit();
                $applied[] = $name;
            } catch (Throwable $e) {
                if ($this->db->pdo()->inTransaction()) {
                    $this->db->pdo()->rollBack();
                }
                throw $e;
            }
        }

        return $applied;
    }

    public function rollbackLastBatch(): array
    {
        $this->ensureMigrationsTable();

        $row = $this->db->fetchOne('SELECT MAX(batch) AS batch FROM migrations');
        $batch = (int) ($row['batch'] ?? 0);
        if ($batch <= 0) {
            return [];
        }

        $rows = $this->db->fetchAll(
            'SELECT migration FROM migrations WHERE batch = :batch ORDER BY id DESC',
            ['batch' => $batch]
        );

        $rolledBack = [];
        foreach ($rows as $item) {
            $file = $this->path . '/' . $item['migration'];
            if (!is_file($file)) {
                throw new RuntimeException('Migration file not found: ' . $item['migration']);
            }

            $migration = require $file;
            if (!$migration instanceof Migration) {
                throw new RuntimeException('Invalid migration: ' . $item['migration']);
            }

            $this->db->pdo()->beginTransaction();
            try {
                $migration->down();
                $this->db->execute('DELETE FROM migrations WHERE migration = :migration', [
                    'migration' => $item['migration'],
                ]);
                $this->db->pdo()->commit();
                $rolledBack[] = $item['migration'];
            } catch (Throwable $e) {
                if ($this->db->pdo()->inTransaction()) {
                    $this->db->pdo()->rollBack();
                }
                throw $e;
            }
        }

        return $rolledBack;
    }

    private function nextBatch(): int
    {
        $this->ensureMigrationsTable();
        $row = $this->db->fetchOne('SELECT COALESCE(MAX(batch), 0) + 1 AS batch FROM migrations');
        return (int) ($row['batch'] ?? 1);
    }
}

