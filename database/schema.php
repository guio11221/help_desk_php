<?php

declare(strict_types=1);

return [
    'extensions' => [
        'pgcrypto',
    ],
    'tables' => [
        'users' => [
            'columns' => [
                'id' => [
                    'type' => 'BIGSERIAL',
                    'primary' => true,
                ],
                'name' => [
                    'type' => 'VARCHAR(120)',
                ],
                'email' => [
                    'type' => 'VARCHAR(180)',
                    'unique' => true,
                ],
                'phone' => [
                    'type' => 'VARCHAR(30)',
                    'nullable' => true,
                ],
                'avatar_path' => [
                    'type' => 'TEXT',
                    'nullable' => true,
                ],
                'password_hash' => [
                    'type' => 'VARCHAR(255)',
                ],
                'role' => [
                    'type' => 'VARCHAR(20)',
                    'default' => "'requester'",
                ],
                'active' => [
                    'type' => 'BOOLEAN',
                    'default' => 'TRUE',
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
            ],
            'indexes' => [],
        ],
        'categories' => [
            'columns' => [
                'id' => [
                    'type' => 'BIGSERIAL',
                    'primary' => true,
                ],
                'name' => [
                    'type' => 'VARCHAR(120)',
                    'unique' => true,
                ],
                'active' => [
                    'type' => 'BOOLEAN',
                    'default' => 'TRUE',
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
            ],
            'indexes' => [],
        ],
        'tickets' => [
            'columns' => [
                'id' => [
                    'type' => 'BIGSERIAL',
                    'primary' => true,
                ],
                'code' => [
                    'type' => 'VARCHAR(30)',
                    'unique' => true,
                ],
                'requester_id' => [
                    'type' => 'BIGINT',
                    'references' => ['table' => 'users', 'column' => 'id'],
                    'on_delete' => 'RESTRICT',
                ],
                'category_id' => [
                    'type' => 'BIGINT',
                    'references' => ['table' => 'categories', 'column' => 'id'],
                    'on_delete' => 'RESTRICT',
                ],
                'assigned_to' => [
                    'type' => 'BIGINT',
                    'nullable' => true,
                    'references' => ['table' => 'users', 'column' => 'id'],
                    'on_delete' => 'SET NULL',
                ],
                'subject' => [
                    'type' => 'VARCHAR(180)',
                ],
                'description' => [
                    'type' => 'TEXT',
                ],
                'priority' => [
                    'type' => 'VARCHAR(20)',
                    'default' => "'medium'",
                ],
                'status' => [
                    'type' => 'VARCHAR(20)',
                    'default' => "'open'",
                ],
                'attachment_path' => [
                    'type' => 'TEXT',
                    'nullable' => true,
                ],
                'attachment_original_name' => [
                    'type' => 'TEXT',
                    'nullable' => true,
                ],
                'attachment_mime' => [
                    'type' => 'TEXT',
                    'nullable' => true,
                ],
                'attachment_size' => [
                    'type' => 'BIGINT',
                    'nullable' => true,
                ],
                'closed_at' => [
                    'type' => 'TIMESTAMP',
                    'nullable' => true,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
            ],
            'indexes' => [
                'idx_tickets_status' => ['columns' => ['status']],
                'idx_tickets_priority' => ['columns' => ['priority']],
                'idx_tickets_requester' => ['columns' => ['requester_id']],
                'idx_tickets_assigned_to' => ['columns' => ['assigned_to']],
            ],
        ],
        'ticket_comments' => [
            'columns' => [
                'id' => [
                    'type' => 'BIGSERIAL',
                    'primary' => true,
                ],
                'ticket_id' => [
                    'type' => 'BIGINT',
                    'references' => ['table' => 'tickets', 'column' => 'id'],
                    'on_delete' => 'CASCADE',
                ],
                'author_id' => [
                    'type' => 'BIGINT',
                    'references' => ['table' => 'users', 'column' => 'id'],
                    'on_delete' => 'RESTRICT',
                ],
                'body' => [
                    'type' => 'TEXT',
                ],
                'is_internal' => [
                    'type' => 'BOOLEAN',
                    'default' => 'FALSE',
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'NOW()',
                ],
            ],
            'indexes' => [
                'idx_ticket_comments_ticket_id' => ['columns' => ['ticket_id']],
            ],
        ],
    ],
];
