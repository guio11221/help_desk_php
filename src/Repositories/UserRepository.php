<?php

declare(strict_types=1);

final class UserRepository
{
    public function __construct(private Database $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne('SELECT * FROM users WHERE email = :email LIMIT 1', [
            'email' => $email,
        ]);
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne('SELECT * FROM users WHERE id = :id LIMIT 1', [
            'id' => $id,
        ]);
    }

    public function create(array $data): int
    {
        $row = $this->db->fetchOne('
            INSERT INTO users (name, email, phone, avatar_path, password_hash, role, active, created_at, updated_at)
            VALUES (:name, :email, :phone, :avatar_path, :password_hash, :role, :active, NOW(), NOW())
            RETURNING id
        ', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
            'password_hash' => $data['password_hash'],
            'role' => $data['role'] ?? 'requester',
            'active' => $data['active'] ?? true,
        ]);

        return (int) ($row['id'] ?? 0);
    }

    public function allAgents(): array
    {
        return $this->db->fetchAll("
            SELECT id, name
            FROM users
            WHERE active = true AND role IN ('agent', 'admin')
            ORDER BY name ASC
        ");
    }

    public function emailExistsForOtherUser(string $email, int $userId): bool
    {
        $row = $this->db->fetchOne('
            SELECT id
            FROM users
            WHERE email = :email AND id <> :id
            LIMIT 1
        ', [
            'email' => $email,
            'id' => $userId,
        ]);

        return $row !== null;
    }

    public function updateProfile(int $userId, array $data): void
    {
        $this->db->execute('
            UPDATE users
            SET name = :name,
                email = :email,
                phone = :phone,
                updated_at = NOW()
            WHERE id = :id
        ', [
            'id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?: null,
        ]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $this->db->execute('
            UPDATE users
            SET password_hash = :password_hash,
                updated_at = NOW()
            WHERE id = :id
        ', [
            'id' => $userId,
            'password_hash' => $passwordHash,
        ]);
    }

    public function updateAvatar(int $userId, ?string $avatarPath): void
    {
        $this->db->execute('
            UPDATE users
            SET avatar_path = :avatar_path,
                updated_at = NOW()
            WHERE id = :id
        ', [
            'id' => $userId,
            'avatar_path' => $avatarPath,
        ]);
    }
}
