<?php

declare(strict_types=1);

return new class(new Database(config('database'))) extends Migration {
    public function up(): void
    {
        $this->execute("ALTER TABLE users ADD COLUMN phone VARCHAR(30)");
        $this->execute("ALTER TABLE users ADD COLUMN avatar_path TEXT");
    }

    public function down(): void
    {
        $this->execute("ALTER TABLE users DROP COLUMN IF EXISTS avatar_path");
        $this->execute("ALTER TABLE users DROP COLUMN IF EXISTS phone");
    }
};
