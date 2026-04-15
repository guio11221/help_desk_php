<?php

declare(strict_types=1);

return new class(new Database(config('database'))) extends Migration {
    public function up(): void
    {
        $this->execute("CREATE EXTENSION IF NOT EXISTS pgcrypto");

        $this->execute("
            CREATE TABLE IF NOT EXISTS users (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(180) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'requester',
                active BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS categories (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(120) NOT NULL UNIQUE,
                active BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS tickets (
                id BIGSERIAL PRIMARY KEY,
                code VARCHAR(30) NOT NULL UNIQUE,
                requester_id BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
                category_id BIGINT NOT NULL REFERENCES categories(id) ON DELETE RESTRICT,
                assigned_to BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
                subject VARCHAR(180) NOT NULL,
                description TEXT NOT NULL,
                priority VARCHAR(20) NOT NULL DEFAULT 'medium',
                status VARCHAR(20) NOT NULL DEFAULT 'open',
                attachment_path TEXT NULL,
                attachment_original_name TEXT NULL,
                attachment_mime TEXT NULL,
                attachment_size BIGINT NULL,
                closed_at TIMESTAMP NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS ticket_comments (
                id BIGSERIAL PRIMARY KEY,
                ticket_id BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
                author_id BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
                body TEXT NOT NULL,
                is_internal BOOLEAN NOT NULL DEFAULT FALSE,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");

        $this->execute("CREATE INDEX IF NOT EXISTS idx_tickets_status ON tickets(status)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_tickets_priority ON tickets(priority)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_tickets_requester ON tickets(requester_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_tickets_assigned_to ON tickets(assigned_to)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_ticket_comments_ticket_id ON ticket_comments(ticket_id)");

        $this->execute("
            INSERT INTO categories (name)
            VALUES
                ('Acesso e senha'),
                ('Sistema'),
                ('Rede'),
                ('Hardware')
            ON CONFLICT (name) DO NOTHING
        ");
    }

    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS ticket_comments");
        $this->execute("DROP TABLE IF EXISTS tickets");
        $this->execute("DROP TABLE IF EXISTS categories");
        $this->execute("DROP TABLE IF EXISTS users");
    }
};

