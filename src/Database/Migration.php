<?php

declare(strict_types=1);

abstract class Migration
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    abstract public function up(): void;

    abstract public function down(): void;

    protected function execute(string $sql): void
    {
        $this->db->execute($sql);
    }
}

