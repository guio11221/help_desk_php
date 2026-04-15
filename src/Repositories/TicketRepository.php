<?php

declare(strict_types=1);

final class TicketRepository
{
    public function __construct(private Database $db)
    {
    }

    public function statsForUser(array $user): array
    {
        $params = [];
        $where = '';

        if (($user['role'] ?? '') === 'requester') {
            $where = 'WHERE requester_id = :user_id';
            $params['user_id'] = $user['id'];
        }

        $rows = $this->db->fetchAll("
            SELECT status, COUNT(*) AS total
            FROM tickets
            {$where}
            GROUP BY status
        ", $params);

        $stats = [
            'open' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'closed' => 0,
        ];

        foreach ($rows as $row) {
            $stats[$row['status']] = (int) $row['total'];
        }

        return $stats;
    }

    public function listForUser(array $user, array $filters = []): array
    {
        $where = [];
        $params = [];

        if (($user['role'] ?? '') === 'requester') {
            $where[] = 't.requester_id = :user_id';
            $params['user_id'] = $user['id'];
        }

        if (!empty($filters['status'])) {
            $where[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $where[] = 't.priority = :priority';
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(t.code ILIKE :search OR t.subject ILIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql = '
            SELECT
                t.*,
                c.name AS category_name,
                req.name AS requester_name,
                ag.name AS agent_name
            FROM tickets t
            LEFT JOIN categories c ON c.id = t.category_id
            LEFT JOIN users req ON req.id = t.requester_id
            LEFT JOIN users ag ON ag.id = t.assigned_to
        ';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY t.updated_at DESC, t.id DESC LIMIT 100';

        return $this->db->fetchAll($sql, $params);
    }

    public function notificationFeed(array $user, int $limit = 5): array
    {
        $where = [];
        $params = [
            'status_open' => 'open',
            'status_progress' => 'in_progress',
            'limit' => $limit,
        ];

        if (($user['role'] ?? '') === 'requester') {
            $where[] = 't.requester_id = :user_id';
            $params['user_id'] = $user['id'];
        }

        $where[] = 't.status IN (:status_open, :status_progress)';

        $sql = '
            SELECT
                t.code,
                t.subject,
                t.status,
                t.priority,
                t.updated_at
            FROM tickets t
        ';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY t.updated_at DESC, t.id DESC LIMIT :limit';

        return $this->db->fetchAll($sql, $params);
    }

    public function notificationCount(array $user): int
    {
        $where = ['status IN (:status_open, :status_progress)'];
        $params = [
            'status_open' => 'open',
            'status_progress' => 'in_progress',
        ];

        if (($user['role'] ?? '') === 'requester') {
            $where[] = 'requester_id = :user_id';
            $params['user_id'] = $user['id'];
        }

        $row = $this->db->fetchOne('
            SELECT COUNT(*) AS total
            FROM tickets
            WHERE ' . implode(' AND ', $where) . '
        ', $params);

        return (int) ($row['total'] ?? 0);
    }

    public function findByCode(string $code): ?array
    {
        return $this->db->fetchOne('
            SELECT
                t.*,
                c.name AS category_name,
                req.name AS requester_name,
                ag.name AS agent_name
            FROM tickets t
            LEFT JOIN categories c ON c.id = t.category_id
            LEFT JOIN users req ON req.id = t.requester_id
            LEFT JOIN users ag ON ag.id = t.assigned_to
            WHERE t.code = :code
            LIMIT 1
        ', ['code' => $code]);
    }

    public function commentsForTicket(int $ticketId): array
    {
        return $this->db->fetchAll('
            SELECT c.*, u.name AS author_name, u.role AS author_role
            FROM ticket_comments c
            JOIN users u ON u.id = c.author_id
            WHERE c.ticket_id = :ticket_id
            ORDER BY c.created_at ASC
        ', ['ticket_id' => $ticketId]);
    }

    public function create(array $data): string
    {
        $code = 'TK' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $this->db->execute('
            INSERT INTO tickets (
                code, requester_id, category_id, assigned_to, subject, description,
                priority, status, attachment_path, attachment_original_name,
                attachment_mime, attachment_size, created_at, updated_at
            )
            VALUES (
                :code, :requester_id, :category_id, :assigned_to, :subject, :description,
                :priority, :status, :attachment_path, :attachment_original_name,
                :attachment_mime, :attachment_size, NOW(), NOW()
            )
        ', [
            'code' => $code,
            'requester_id' => $data['requester_id'],
            'category_id' => $data['category_id'],
            'assigned_to' => $data['assigned_to'] ?: null,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => $data['status'] ?? 'open',
            'attachment_path' => $data['attachment_path'] ?? null,
            'attachment_original_name' => $data['attachment_original_name'] ?? null,
            'attachment_mime' => $data['attachment_mime'] ?? null,
            'attachment_size' => $data['attachment_size'] ?? null,
        ]);

        return $code;
    }

    public function addComment(array $data): void
    {
        $this->db->execute('
            INSERT INTO ticket_comments (ticket_id, author_id, body, is_internal, created_at, updated_at)
            VALUES (:ticket_id, :author_id, :body, :is_internal, NOW(), NOW())
        ', [
            'ticket_id' => $data['ticket_id'],
            'author_id' => $data['author_id'],
            'body' => $data['body'],
            'is_internal' => $data['is_internal'] ?? false,
        ]);
    }

    public function updateStatus(string $code, string $status, ?int $assignedTo = null): void
    {
        $this->db->execute("
            UPDATE tickets
            SET
                status = :status,
                assigned_to = COALESCE(:assigned_to, assigned_to),
                updated_at = NOW(),
                closed_at = CASE WHEN :status = 'closed' THEN NOW() ELSE closed_at END
            WHERE code = :code
        ", [
            'code' => $code,
            'status' => $status,
            'assigned_to' => $assignedTo,
        ]);
    }
}
