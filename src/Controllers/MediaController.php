<?php

declare(strict_types=1);

final class MediaController extends BaseController
{
    public function avatar(): void
    {
        require_auth();

        $userId = (int) $this->input('user', 0);
        if ($userId <= 0) {
            http_response_code(404);
            exit('Avatar nao encontrado.');
        }

        $user = (new UserRepository($this->db))->findById($userId);
        if (!$user || empty($user['avatar_path'])) {
            http_response_code(404);
            exit('Avatar nao encontrado.');
        }

        $relativePath = str_replace(['..', '\\'], '', (string) $user['avatar_path']);
        $fullPath = realpath(BASE_PATH . '/' . ltrim($relativePath, '/'));
        $storagePath = realpath(BASE_PATH . '/storage');

        if ($fullPath === false || $storagePath === false || !str_starts_with($fullPath, $storagePath) || !is_file($fullPath)) {
            http_response_code(404);
            exit('Avatar nao encontrado.');
        }

        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string) filesize($fullPath));
        header('Cache-Control: private, max-age=86400');
        readfile($fullPath);
        exit;
    }
}
