<?php

declare(strict_types=1);

return new class(new Database(config('database'))) extends Migration {
    public function up(): void
    {
        $statements = array (
          0 => 'ALTER TABLE users DROP COLUMN IF EXISTS phone',
        );

        foreach ($statements as $sql) {
            $this->execute($sql);
        }
    }

    public function down(): void
    {
        $statements = array (
          0 => 'ALTER TABLE users ADD COLUMN phone VARCHAR(30)',
        );

        foreach ($statements as $sql) {
            $this->execute($sql);
        }
    }
};
