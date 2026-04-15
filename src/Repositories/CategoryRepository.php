<?php

declare(strict_types=1);

final class CategoryRepository
{
    public function __construct(private Database $db)
    {
    }

    public function allActive(): array
    {
        return $this->db->fetchAll('
            SELECT id, name
            FROM categories
            WHERE active = true
            ORDER BY name ASC
        ');
    }
}

