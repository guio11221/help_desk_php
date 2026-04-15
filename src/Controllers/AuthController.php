<?php

declare(strict_types=1);

final class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (auth_check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Entrar',
            'extraCss' => ['assets/css/login.css'],
            'hideNavbar' => true,
            'fullPage' => true,
        ]);
    }

    public function login(): void
    {
        verify_csrf();

        $email = strtolower(trim((string) $this->input('email')));
        $password = (string) $this->input('password');

        preserve_old_input(['email' => $email]);

        if (!Validator::email($email) || $password === '') {
            flash('error', 'Informe e-mail e senha validos.');
            $this->redirect('/login');
        }

        $user = (new UserRepository($this->db))->findByEmail($email);

        if (!$user || !$user['active'] || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Credenciais invalidas.');
            $this->redirect('/login');
        }

        unset($user['password_hash']);

        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        clear_old_input();

        flash('success', 'Login realizado com sucesso.');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        verify_csrf();

        $_SESSION = [];
        session_destroy();

        redirect('/login');
    }
}
