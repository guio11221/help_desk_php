<?php

declare(strict_types=1);

abstract class BaseController
{
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database(config('database'));
    }

    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function redirect(string $path): void
    {
        redirect($path);
    }
}

