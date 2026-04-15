<?php

declare(strict_types=1);

final class SchemaDiff
{
    public function diff(array $from, array $to): array
    {
        $up = [];
        $down = [];

        $from = SchemaState::normalize($from);
        $to = SchemaState::normalize($to);

        foreach (array_diff($to['extensions'], $from['extensions']) as $extension) {
            $up[] = SchemaSqlBuilder::createExtension($extension);
        }

        foreach ($to['tables'] as $tableName => $table) {
            if (!isset($from['tables'][$tableName])) {
                $up[] = SchemaSqlBuilder::createTable($tableName, $table);
                foreach ($table['indexes'] as $indexName => $index) {
                    $up[] = SchemaSqlBuilder::createIndex($tableName, $indexName, $index);
                }
                foreach (array_reverse($table['indexes'], true) as $indexName => $index) {
                    $down[] = SchemaSqlBuilder::dropIndex($indexName);
                }
                $down[] = SchemaSqlBuilder::dropTable($tableName);
                continue;
            }

            $tableDiff = $this->diffTable($tableName, $from['tables'][$tableName], $table);
            $up = array_merge($up, $tableDiff['up']);
            $down = array_merge($tableDiff['down'], $down);
        }

        foreach ($from['tables'] as $tableName => $table) {
            if (!isset($to['tables'][$tableName])) {
                $up[] = SchemaSqlBuilder::dropTable($tableName);
                $down[] = SchemaSqlBuilder::createTable($tableName, $table);
                foreach ($table['indexes'] as $indexName => $index) {
                    $down[] = SchemaSqlBuilder::createIndex($tableName, $indexName, $index);
                }
            }
        }

        return [
            'up' => $up,
            'down' => $down,
        ];
    }

    private function diffTable(string $tableName, array $from, array $to): array
    {
        $up = [];
        $down = [];

        foreach ($to['columns'] as $columnName => $column) {
            if (!isset($from['columns'][$columnName])) {
                $up[] = SchemaSqlBuilder::addColumn($tableName, $columnName, $column);
                $down[] = SchemaSqlBuilder::dropColumn($tableName, $columnName);
                continue;
            }

            if ($from['columns'][$columnName] !== $column) {
                $up = array_merge($up, SchemaSqlBuilder::alterColumn($tableName, $columnName, $from['columns'][$columnName], $column));
                $down = array_merge(
                    SchemaSqlBuilder::alterColumn($tableName, $columnName, $column, $from['columns'][$columnName]),
                    $down
                );
            }
        }

        foreach ($from['columns'] as $columnName => $column) {
            if (!isset($to['columns'][$columnName])) {
                $up[] = SchemaSqlBuilder::dropColumn($tableName, $columnName);
                $down[] = SchemaSqlBuilder::addColumn($tableName, $columnName, $column);
            }
        }

        foreach ($to['indexes'] as $indexName => $index) {
            if (!isset($from['indexes'][$indexName])) {
                $up[] = SchemaSqlBuilder::createIndex($tableName, $indexName, $index);
                $down[] = SchemaSqlBuilder::dropIndex($indexName);
                continue;
            }

            if ($from['indexes'][$indexName] !== $index) {
                $up[] = SchemaSqlBuilder::dropIndex($indexName);
                $up[] = SchemaSqlBuilder::createIndex($tableName, $indexName, $index);

                $down[] = SchemaSqlBuilder::dropIndex($indexName);
                $down[] = SchemaSqlBuilder::createIndex($tableName, $indexName, $from['indexes'][$indexName]);
            }
        }

        foreach ($from['indexes'] as $indexName => $index) {
            if (!isset($to['indexes'][$indexName])) {
                $up[] = SchemaSqlBuilder::dropIndex($indexName);
                $down[] = SchemaSqlBuilder::createIndex($tableName, $indexName, $index);
            }
        }

        return [
            'up' => $up,
            'down' => $down,
        ];
    }
}

