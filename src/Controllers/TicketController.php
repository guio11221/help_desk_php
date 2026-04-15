<?php

declare(strict_types=1);

final class TicketController extends BaseController
{
    public function index(): void
    {
        require_auth();

        $filters = [
            'status' => trim((string) $this->input('status', '')),
            'priority' => trim((string) $this->input('priority', '')),
            'search' => trim((string) $this->input('search', '')),
        ];

        $tickets = (new TicketRepository($this->db))->listForUser(current_user(), $filters);

        $this->view('tickets/index', [
            'tickets' => $tickets,
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        require_auth();

        $this->view('tickets/create', [
            'categories' => (new CategoryRepository($this->db))->allActive(),
            'agents' => (new UserRepository($this->db))->allAgents(),
        ]);
    }

    public function store(): void
    {
        require_auth();
        verify_csrf();

        $subject = trim((string) $this->input('subject'));
        $description = trim((string) $this->input('description'));
        $categoryId = (int) $this->input('category_id');
        $priority = (string) $this->input('priority', 'medium');
        $assignedTo = (int) $this->input('assigned_to', 0);

        preserve_old_input($_POST);

        if (!Validator::minLength($subject, 8) || !Validator::minLength($description, 15)) {
            flash('error', 'Assunto e descricao precisam ser mais detalhados.');
            $this->redirect('/tickets/create');
        }

        if (!Validator::in($priority, ['low', 'medium', 'high', 'urgent'])) {
            flash('error', 'Prioridade invalida.');
            $this->redirect('/tickets/create');
        }

        $upload = $this->handleUpload();

        $code = (new TicketRepository($this->db))->create([
            'requester_id' => current_user()['id'],
            'category_id' => $categoryId,
            'assigned_to' => $assignedTo > 0 ? $assignedTo : null,
            'subject' => $subject,
            'description' => $description,
            'priority' => $priority,
            'attachment_path' => $upload['path'] ?? null,
            'attachment_original_name' => $upload['original_name'] ?? null,
            'attachment_mime' => $upload['mime'] ?? null,
            'attachment_size' => $upload['size'] ?? null,
        ]);

        clear_old_input();
        flash('success', 'Chamado criado com sucesso.');
        $this->redirect('/tickets/show?code=' . urlencode($code));
    }

    public function show(): void
    {
        require_auth();

        $code = (string) $this->input('code');
        $repo = new TicketRepository($this->db);
        $ticket = $repo->findByCode($code);

        if (!$ticket) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $user = current_user();
        if ($user['role'] === 'requester' && (int) $ticket['requester_id'] !== (int) $user['id']) {
            http_response_code(403);
            exit('Acesso negado.');
        }

        $this->view('tickets/show', [
            'ticket' => $ticket,
            'comments' => $repo->commentsForTicket((int) $ticket['id']),
            'agents' => (new UserRepository($this->db))->allAgents(),
        ]);
    }

    public function comment(): void
    {
        require_auth();
        verify_csrf();

        $code = (string) $this->input('code');
        $body = trim((string) $this->input('body'));
        $isInternal = $this->input('is_internal') ? true : false;

        if (!Validator::minLength($body, 3)) {
            flash('error', 'Comentario invalido.');
            $this->redirect('/tickets/show?code=' . urlencode($code));
        }

        $repo = new TicketRepository($this->db);
        $ticket = $repo->findByCode($code);
        if (!$ticket) {
            http_response_code(404);
            exit('Chamado nao encontrado.');
        }

        if (current_user()['role'] === 'requester') {
            $isInternal = false;
        }

        $repo->addComment([
            'ticket_id' => $ticket['id'],
            'author_id' => current_user()['id'],
            'body' => $body,
            'is_internal' => $isInternal,
        ]);

        flash('success', 'Comentario adicionado.');
        $this->redirect('/tickets/show?code=' . urlencode($code));
    }

    public function status(): void
    {
        require_auth();
        require_role(['agent', 'admin']);
        verify_csrf();

        $code = (string) $this->input('code');
        $status = (string) $this->input('status');
        $assignedTo = (int) $this->input('assigned_to', 0);

        if (!Validator::in($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            flash('error', 'Status invalido.');
            $this->redirect('/tickets/show?code=' . urlencode($code));
        }

        (new TicketRepository($this->db))->updateStatus(
            $code,
            $status,
            $assignedTo > 0 ? $assignedTo : null
        );

        flash('success', 'Status atualizado.');
        $this->redirect('/tickets/show?code=' . urlencode($code));
    }

    private function handleUpload(): array
    {
        if (empty($_FILES['attachment']['name'])) {
            return [];
        }

        $file = $_FILES['attachment'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('error', 'Falha no upload do anexo.');
            $this->redirect('/tickets/create');
        }

        $maxBytes = (int) config('app.max_upload_mb', 10) * 1024 * 1024;
        if (($file['size'] ?? 0) > $maxBytes) {
            flash('error', 'Arquivo acima do limite permitido.');
            $this->redirect('/tickets/create');
        }

        $relativeDir = trim((string) config('app.upload_dir', 'storage/uploads'), '/');
        $uploadDir = BASE_PATH . '/' . $relativeDir;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $originalName = basename((string) $file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $safeName = bin2hex(random_bytes(16)) . ($extension !== '' ? '.' . $extension : '');
        $target = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            flash('error', 'Nao foi possivel salvar o anexo.');
            $this->redirect('/tickets/create');
        }

        return [
            'path' => $relativeDir . '/' . $safeName,
            'original_name' => $originalName,
            'mime' => $file['type'] ?? 'application/octet-stream',
            'size' => (int) $file['size'],
        ];
    }
}

