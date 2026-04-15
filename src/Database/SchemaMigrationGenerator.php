<?php

declare(strict_types=1);

final class SchemaMigrationGenerator
{
    public function __construct(
        private string $schemaFile,
        private string $snapshotFile,
        private string $migrationsPath
    ) {
    }

    public function generate(string $name): array
    {
        $schema = $this->loadSchema();
        $snapshot = $this->loadSnapshot();

        $diff = (new SchemaDiff())->diff($snapshot, $schema);
        if (!$diff['up']) {
            return ['created' => false, 'file' => null, 'snapshot' => false];
        }

        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . '_' . $name . '.php';
        $filePath = $this->migrationsPath . '/' . $fileName;

        $content = $this->buildMigration($diff['up'], $diff['down']);
        if (file_put_contents($filePath, $content) === false) {
            throw new RuntimeException('Failed to create migration file.');
        }

        if (file_put_contents($this->snapshotFile, json_encode(SchemaState::normalize($schema), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
            throw new RuntimeException('Failed to update schema snapshot.');
        }

        return ['created' => true, 'file' => $fileName, 'snapshot' => true];
    }

    private function loadSchema(): array
    {
        $schema = require $this->schemaFile;
        if (!is_array($schema)) {
            throw new RuntimeException('database/schema.php must return an array.');
        }

        return $schema;
    }

    private function loadSnapshot(): array
    {
        if (!is_file($this->snapshotFile)) {
            return ['extensions' => [], 'tables' => []];
        }

        $content = file_get_contents($this->snapshotFile);
        $data = json_decode($content ?: '{}', true);
        if (!is_array($data)) {
            throw new RuntimeException('Invalid schema snapshot JSON.');
        }

        return $data;
    }

    private function buildMigration(array $up, array $down): string
    {
        return "<?php\n\ndeclare(strict_types=1);\n\nreturn new class(new Database(config('database'))) extends Migration {\n" .
            "    public function up(): void\n    {\n" .
            $this->buildSqlLoop($up) .
            "    }\n\n" .
            "    public function down(): void\n    {\n" .
            $this->buildSqlLoop($down) .
            "    }\n};\n";
    }

    private function buildSqlLoop(array $sqlStatements): string
    {
        if (!$sqlStatements) {
            return "        // No changes.\n";
        }

        $export = var_export(array_values($sqlStatements), true);
        $lines = explode("\n", $export);
        $indented = [];
        foreach ($lines as $line) {
            $indented[] = '        ' . $line;
        }

        return "        \$statements = " . ltrim(implode("\n", $indented)) . ";\n\n" .
            "        foreach (\$statements as \$sql) {\n" .
            "            \$this->execute(\$sql);\n" .
            "        }\n";
    }
}

