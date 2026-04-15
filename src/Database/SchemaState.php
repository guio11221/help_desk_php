<?php

declare(strict_types=1);

final class SchemaState
{
    public static function normalize(array $schema): array
    {
        $normalized = [
            'extensions' => array_values($schema['extensions'] ?? []),
            'tables' => [],
        ];

        sort($normalized['extensions']);

        foreach (($schema['tables'] ?? []) as $tableName => $table) {
            $columns = $table['columns'] ?? [];
            ksort($columns);

            $indexes = [];
            foreach (($table['indexes'] ?? []) as $indexName => $index) {
                $index['columns'] = array_values($index['columns'] ?? []);
                $indexes[$indexName] = $index;
            }
            ksort($indexes);

            $normalized['tables'][$tableName] = [
                'columns' => $columns,
                'indexes' => $indexes,
            ];
        }

        ksort($normalized['tables']);

        return $normalized;
    }
}

