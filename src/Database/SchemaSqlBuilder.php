<?php

declare(strict_types=1);

final class SchemaSqlBuilder
{
    public static function createExtension(string $name): string
    {
        return 'CREATE EXTENSION IF NOT EXISTS ' . $name;
    }

    public static function createTable(string $tableName, array $table): string
    {
        $parts = [];

        foreach ($table['columns'] as $columnName => $column) {
            $parts[] = self::columnDefinition($columnName, $column);
        }

        return "CREATE TABLE {$tableName} (\n    " . implode(",\n    ", $parts) . "\n)";
    }

    public static function dropTable(string $tableName): string
    {
        return 'DROP TABLE IF EXISTS ' . $tableName;
    }

    public static function addColumn(string $tableName, string $columnName, array $column): string
    {
        return 'ALTER TABLE ' . $tableName . ' ADD COLUMN ' . self::columnDefinition($columnName, $column);
    }

    public static function dropColumn(string $tableName, string $columnName): string
    {
        return 'ALTER TABLE ' . $tableName . ' DROP COLUMN IF EXISTS ' . $columnName;
    }

    public static function alterColumn(string $tableName, string $columnName, array $from, array $to): array
    {
        $sql = [];

        if (($from['type'] ?? null) !== ($to['type'] ?? null)) {
            $sql[] = 'ALTER TABLE ' . $tableName . ' ALTER COLUMN ' . $columnName . ' TYPE ' . $to['type'];
        }

        if (($from['nullable'] ?? false) !== ($to['nullable'] ?? false)) {
            $sql[] = 'ALTER TABLE ' . $tableName . ' ALTER COLUMN ' . $columnName .
                (($to['nullable'] ?? false) ? ' DROP NOT NULL' : ' SET NOT NULL');
        }

        $fromDefault = array_key_exists('default', $from) ? $from['default'] : null;
        $toDefault = array_key_exists('default', $to) ? $to['default'] : null;
        if ($fromDefault !== $toDefault) {
            if ($toDefault === null) {
                $sql[] = 'ALTER TABLE ' . $tableName . ' ALTER COLUMN ' . $columnName . ' DROP DEFAULT';
            } else {
                $sql[] = 'ALTER TABLE ' . $tableName . ' ALTER COLUMN ' . $columnName . ' SET DEFAULT ' . $toDefault;
            }
        }

        $fromPrimary = (bool) ($from['primary'] ?? false);
        $toPrimary = (bool) ($to['primary'] ?? false);
        if ($fromPrimary !== $toPrimary) {
            if ($toPrimary) {
                $sql[] = 'ALTER TABLE ' . $tableName . ' ADD PRIMARY KEY (' . $columnName . ')';
            } else {
                $sql[] = 'ALTER TABLE ' . $tableName . ' DROP CONSTRAINT IF EXISTS ' . $tableName . '_pkey';
            }
        }

        $fromUnique = (bool) ($from['unique'] ?? false);
        $toUnique = (bool) ($to['unique'] ?? false);
        if ($fromUnique !== $toUnique) {
            $constraint = $tableName . '_' . $columnName . '_key';
            if ($toUnique) {
                $sql[] = 'ALTER TABLE ' . $tableName . ' ADD CONSTRAINT ' . $constraint . ' UNIQUE (' . $columnName . ')';
            } else {
                $sql[] = 'ALTER TABLE ' . $tableName . ' DROP CONSTRAINT IF EXISTS ' . $constraint;
            }
        }

        $fromReference = self::referenceSignature($from);
        $toReference = self::referenceSignature($to);
        $constraint = $tableName . '_' . $columnName . '_fkey';
        if ($fromReference !== $toReference) {
            if ($fromReference !== null) {
                $sql[] = 'ALTER TABLE ' . $tableName . ' DROP CONSTRAINT IF EXISTS ' . $constraint;
            }
            if ($toReference !== null) {
                $sql[] = 'ALTER TABLE ' . $tableName . ' ADD CONSTRAINT ' . $constraint . ' ' . self::foreignKeyClause($columnName, $to);
            }
        }

        return $sql;
    }

    public static function createIndex(string $tableName, string $indexName, array $index): string
    {
        $unique = !empty($index['unique']) ? 'UNIQUE ' : '';
        return 'CREATE ' . $unique . 'INDEX ' . $indexName . ' ON ' . $tableName .
            ' (' . implode(', ', $index['columns']) . ')';
    }

    public static function dropIndex(string $indexName): string
    {
        return 'DROP INDEX IF EXISTS ' . $indexName;
    }

    private static function columnDefinition(string $columnName, array $column): string
    {
        $parts = [$columnName, $column['type']];

        if (!($column['nullable'] ?? false)) {
            $parts[] = 'NOT NULL';
        }

        if (array_key_exists('default', $column) && $column['default'] !== null) {
            $parts[] = 'DEFAULT ' . $column['default'];
        }

        if (!empty($column['primary'])) {
            $parts[] = 'PRIMARY KEY';
        } elseif (!empty($column['unique'])) {
            $parts[] = 'UNIQUE';
        }

        if (self::referenceSignature($column) !== null) {
            $parts[] = self::foreignKeyClause($columnName, $column);
        }

        return implode(' ', $parts);
    }

    private static function foreignKeyClause(string $columnName, array $column): string
    {
        $references = $column['references'];
        $onDelete = $column['on_delete'] ?? 'RESTRICT';

        return 'REFERENCES ' . $references['table'] . '(' . $references['column'] . ') ON DELETE ' . $onDelete;
    }

    private static function referenceSignature(array $column): ?string
    {
        if (empty($column['references'])) {
            return null;
        }

        return json_encode([
            'table' => $column['references']['table'] ?? null,
            'column' => $column['references']['column'] ?? null,
            'on_delete' => $column['on_delete'] ?? 'RESTRICT',
        ]);
    }
}

