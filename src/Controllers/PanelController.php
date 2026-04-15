<?php

declare(strict_types=1);

final class PanelController extends BaseController
{
    public function profile(): void
    {
        require_auth();

        $user = $this->freshUser();

        $this->view('panel/profile', [
            'title' => 'Perfil',
            'extraCss' => ['assets/css/panel.css'],
            'user' => $user,
        ]);
    }

    public function updateProfile(): void
    {
        require_auth();
        verify_csrf();

        $user = $this->freshUser();
        $userId = (int) $user['id'];
        $name = trim((string) $this->input('name'));
        $email = strtolower(trim((string) $this->input('email')));
        $phone = trim((string) $this->input('phone'));

        preserve_old_input([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        if (!Validator::minLength($name, 3)) {
            flash('error', 'Informe um nome valido.');
            $this->redirect('/panel/profile');
        }

        if (!Validator::email($email)) {
            flash('error', 'Informe um e-mail valido.');
            $this->redirect('/panel/profile');
        }

        $repository = new UserRepository($this->db);
        if ($repository->emailExistsForOtherUser($email, $userId)) {
            flash('error', 'Este e-mail ja esta em uso.');
            $this->redirect('/panel/profile');
        }

        $repository->updateProfile($userId, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        clear_old_input();
        $this->refreshSessionUser($userId);
        flash('success', 'Perfil atualizado com sucesso.');
        $this->redirect('/panel/profile');
    }

    public function updatePassword(): void
    {
        require_auth();
        verify_csrf();

        $user = $this->freshUser();
        $currentPassword = (string) $this->input('current_password');
        $newPassword = (string) $this->input('new_password');
        $newPasswordConfirmation = (string) $this->input('new_password_confirmation');

        if ($currentPassword === '' || $newPassword === '' || $newPasswordConfirmation === '') {
            flash('error', 'Preencha todos os campos de senha.');
            $this->redirect('/panel/profile');
        }

        if (!password_verify($currentPassword, (string) ($user['password_hash'] ?? ''))) {
            flash('error', 'A senha atual esta incorreta.');
            $this->redirect('/panel/profile');
        }

        if (!Validator::minLength($newPassword, 8)) {
            flash('error', 'A nova senha precisa ter ao menos 8 caracteres.');
            $this->redirect('/panel/profile');
        }

        if (!hash_equals($newPassword, $newPasswordConfirmation)) {
            flash('error', 'A confirmacao de senha nao confere.');
            $this->redirect('/panel/profile');
        }

        (new UserRepository($this->db))->updatePassword(
            (int) $user['id'],
            password_hash($newPassword, PASSWORD_DEFAULT)
        );

        $this->refreshSessionUser((int) $user['id']);
        flash('success', 'Senha atualizada com sucesso.');
        $this->redirect('/panel/profile');
    }

    public function updateAvatar(): void
    {
        require_auth();
        verify_csrf();

        $user = $this->freshUser();
        $avatarPath = $this->handleAvatarUpload();

        (new UserRepository($this->db))->updateAvatar((int) $user['id'], $avatarPath);

        $this->refreshSessionUser((int) $user['id']);
        flash('success', 'Foto de perfil atualizada.');
        $this->redirect('/panel/profile');
    }

    public function settings(): void
    {
        require_auth();

        $this->view('panel/settings', [
            'title' => 'Configuracoes',
            'extraCss' => ['assets/css/panel.css'],
            'user' => current_user(),
        ]);
    }

    public function theme(): void
    {
        require_auth();

        $this->view('panel/theme', [
            'title' => 'Temas',
            'extraCss' => ['assets/css/panel.css'],
            'currentTheme' => app_theme(),
        ]);
    }

    public function updateTheme(): void
    {
        require_auth();
        verify_csrf();

        $theme = (string) $this->input('theme', 'business');
        $redirectTo = (string) $this->input('redirect_to', '/dashboard');
        $allowed = ['business', 'graphite', 'forest'];
        if (!in_array($theme, $allowed, true)) {
            flash('error', 'Tema invalido.');
            $this->redirect('/panel/theme');
        }

        $_SESSION['theme'] = $theme;
        flash('success', 'Tema atualizado.');
        if ($redirectTo === '' || $redirectTo[0] !== '/') {
            $redirectTo = '/dashboard';
        }

        $this->redirect($redirectTo);
    }

    private function freshUser(): array
    {
        $sessionUser = current_user();
        $user = $sessionUser ? (new UserRepository($this->db))->findById((int) $sessionUser['id']) : null;

        if (!$user) {
            $_SESSION = [];
            session_destroy();
            redirect('/login');
        }

        return $user;
    }

    private function refreshSessionUser(int $userId): void
    {
        $user = (new UserRepository($this->db))->findById($userId);
        if (!$user) {
            return;
        }

        unset($user['password_hash']);
        $_SESSION['user'] = $user;
    }

    private function handleAvatarUpload(): string
    {
        if (empty($_FILES['avatar']['name'])) {
            flash('error', 'Selecione uma imagem para upload.');
            $this->redirect('/panel/profile');
        }

        $file = $_FILES['avatar'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('error', 'Falha no upload da imagem.');
            $this->redirect('/panel/profile');
        }

        $maxBytes = (int) config('app.max_upload_mb', 10) * 1024 * 1024;
        if (($file['size'] ?? 0) > $maxBytes) {
            flash('error', 'Imagem acima do limite permitido.');
            $this->redirect('/panel/profile');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? (string) finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            flash('error', 'Formato de imagem invalido.');
            $this->redirect('/panel/profile');
        }

        $relativeDir = trim((string) config('app.upload_dir', 'storage/uploads'), '/');
        $uploadDir = BASE_PATH . '/' . $relativeDir;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $safeName = 'avatar_' . (int) current_user()['id'] . '_' . bin2hex(random_bytes(10)) . '.' . $allowed[$mime];
        $target = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            flash('error', 'Nao foi possivel salvar a foto.');
            $this->redirect('/panel/profile');
        }

        return $relativeDir . '/' . $safeName;
    }
}
