<?php
$title = $title ?? config('app.name', 'Help Desk');
$user = current_user();
$extraCss = $extraCss ?? [];
$hideNavbar = $hideNavbar ?? false;
$fullPage = $fullPage ?? false;
$useSidebar = $user && !$hideNavbar && !$fullPage;
$theme = app_theme();
?>
<!doctype html>
<html lang="pt-br" data-theme="<?= e($theme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= asset('assets/css/app.css') ?>" rel="stylesheet">
    <?php foreach ($extraCss as $cssFile): ?>
        <link href="<?= asset($cssFile) ?>" rel="stylesheet">
    <?php endforeach; ?>
</head>
<body class="theme-<?= e($theme) ?>">
<div class="page-shell">
    <?php if ($useSidebar): ?>
        <?php
        $userAvatar = avatar_url($user);
        $notifications = app_notifications();
        $themeChoices = [
            'business' => 'Business',
            'graphite' => 'Graphite',
            'forest' => 'Forest',
        ];
        $pageGroup = 'Operacao';
        $quickRoutes = [
            ['label' => 'Dashboard', 'hint' => 'Visao geral do painel', 'url' => base_url('/dashboard')],
            ['label' => 'Chamados', 'hint' => 'Fila completa de tickets', 'url' => base_url('/tickets')],
            ['label' => 'Novo chamado', 'hint' => 'Abrir um novo ticket', 'url' => base_url('/tickets/create')],
            ['label' => 'Perfil', 'hint' => 'Dados da sua conta', 'url' => base_url('/panel/profile')],
            ['label' => 'Configuracoes', 'hint' => 'Preferencias do painel', 'url' => base_url('/panel/settings')],
        ];
        if (path_starts_with('/panel')) {
            $pageGroup = 'Conta';
        } elseif (path_starts_with('/tickets')) {
            $pageGroup = 'Chamados';
        }
        ?>
        <div class="app-layout">
            <aside class="app-sidebar">
                <div class="app-sidebar__top">
                    <div class="app-sidebar__brand">
                        <div class="app-sidebar__logo">HD</div>
                        <div>
                            <div class="app-sidebar__title"><?= e(config('app.name')) ?></div>
                            <div class="app-sidebar__subtitle">Painel administrativo</div>
                        </div>
                    </div>

                    <nav class="app-nav">
                        <div class="app-nav__section">Principal</div>
                        <a class="app-nav__item <?= path_is('/dashboard') ? 'is-active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                            <span class="app-nav__icon"></span>
                            <span>Dashboard</span>
                        </a>
                        <a class="app-nav__item <?= path_starts_with('/tickets') && !path_is('/tickets/create') ? 'is-active' : '' ?>" href="<?= base_url('/tickets') ?>">
                            <span class="app-nav__icon"></span>
                            <span>Chamados</span>
                        </a>
                        <a class="app-nav__item <?= path_is('/tickets/create') ? 'is-active' : '' ?>" href="<?= base_url('/tickets/create') ?>">
                            <span class="app-nav__icon"></span>
                            <span>Novo chamado</span>
                        </a>

                        <div class="app-nav__section">Conta</div>
                        <a class="app-nav__item <?= path_is('/panel/profile') ? 'is-active' : '' ?>" href="<?= base_url('/panel/profile') ?>">
                            <span class="app-nav__icon"></span>
                            <span>Perfil</span>
                        </a>
                        <a class="app-nav__item <?= path_is('/panel/settings') ? 'is-active' : '' ?>" href="<?= base_url('/panel/settings') ?>">
                            <span class="app-nav__icon"></span>
                            <span>Configuracoes</span>
                        </a>
                        <a class="app-nav__item <?= path_is('/panel/theme') ? 'is-active' : '' ?>" href="<?= base_url('/panel/theme') ?>">
                            <span class="app-nav__icon"></span>
                            <span>Temas</span>
                        </a>
                    </nav>
                </div>

                <div class="app-sidebar__footer">
                    <div class="app-user">
                        <?php if ($userAvatar): ?>
                            <img class="app-user__avatar app-user__avatar--image" src="<?= e($userAvatar) ?>" alt="Foto do usuario">
                        <?php else: ?>
                            <div class="app-user__avatar"><?= e(strtoupper(substr((string) ($user['name'] ?? 'U'), 0, 1))) ?></div>
                        <?php endif; ?>
                        <div class="app-user__meta">
                            <strong><?= e($user['name'] ?? 'Usuario') ?></strong>
                            <span><?= e($user['role'] ?? 'user') ?></span>
                        </div>
                    </div>

                    <form method="post" action="<?= base_url('/logout') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-warning btn-sm w-100">Sair</button>
                    </form>
                </div>
            </aside>

            <div class="app-content">
                <header class="app-topbar">
                    <div class="app-topbar__main">
                        <div class="app-topbar__intro">
                            <div class="app-topbar__breadcrumb"><?= e($pageGroup) ?> / <span><?= e($title) ?></span></div>
                            <h1 class="app-topbar__title"><?= e($title) ?></h1>
                        </div>

                        <div class="app-route-search" data-route-search>
                            <div class="app-route-search__icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l3.93 3.92 1.06-1.06-3.92-3.93A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"></path></svg>
                            </div>
                            <input
                                class="app-route-search__input"
                                type="search"
                                placeholder="Buscar paginas, modulos e atalhos..."
                                autocomplete="off"
                                data-route-search-input
                            >
                            <span class="app-route-search__hint">Ctrl K</span>
                            <div class="app-route-search__dropdown" data-route-search-results>
                                <?php foreach ($quickRoutes as $route): ?>
                                    <a class="app-route-search__item" href="<?= e($route['url']) ?>" data-route-label="<?= e(strtolower($route['label'] . ' ' . $route['hint'])) ?>">
                                        <strong><?= e($route['label']) ?></strong>
                                        <span><?= e($route['hint']) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="app-topbar__actions">
                        <div class="app-notifications">
                            <button class="app-icon-button" type="button" aria-label="Notificacoes de chamados" title="Notificacoes">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5.75A2.75 2.75 0 0 1 7.75 3h8.5A2.75 2.75 0 0 1 19 5.75v6.5A2.75 2.75 0 0 1 16.25 15h-4.19l-3.66 3.13A.75.75 0 0 1 7 17.56V15.1A2.75 2.75 0 0 1 5 12.25Z"></path></svg>
                                <?php if (($notifications['count'] ?? 0) > 0): ?>
                                    <span class="app-icon-button__badge"><?= (int) min(99, (int) $notifications['count']) ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="app-notifications__dropdown">
                                <div class="app-notifications__head">
                                    <strong>Notificacoes</strong>
                                    <span><?= (int) ($notifications['count'] ?? 0) ?> ativas</span>
                                </div>
                                <div class="app-notifications__list">
                                    <?php if (!empty($notifications['items'])): ?>
                                        <?php foreach ($notifications['items'] as $item): ?>
                                            <a class="app-notifications__item" href="<?= base_url('/tickets/show?code=' . urlencode((string) $item['code'])) ?>">
                                                <strong><?= e($item['code']) ?> · <?= e($item['subject']) ?></strong>
                                                <span><?= e($item['status']) ?> · <?= e($item['priority']) ?> · <?= e($item['updated_at']) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="app-notifications__empty">Nenhuma notificacao pendente.</div>
                                    <?php endif; ?>
                                </div>
                                <a class="app-notifications__footer" href="<?= base_url('/tickets') ?>">Ver fila completa</a>
                            </div>
                        </div>
                        <div class="app-theme-switcher">
                            <button class="app-icon-button" type="button" aria-label="Alterar tema" title="Tema">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.25a8.75 8.75 0 1 0 8.75 8.75A8.76 8.76 0 0 0 12 3.25Zm0 1.5a7.23 7.23 0 0 1 4.63 1.67c-.78.46-1.27 1.25-1.27 2.16 0 1.42 1.18 2.57 2.64 2.57.44 0 .87-.1 1.25-.3.01.12.02.24.02.36A7.25 7.25 0 1 1 12 4.75Z"></path></svg>
                            </button>
                            <div class="app-theme-switcher__dropdown">
                                <div class="app-theme-switcher__head">
                                    <strong>Tema do painel</strong>
                                    <span>Aplicacao imediata</span>
                                </div>
                                <form method="post" action="<?= base_url('/panel/theme') ?>" class="app-theme-switcher__form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="redirect_to" value="<?= e(request_path()) ?>">
                                    <?php foreach ($themeChoices as $themeKey => $themeLabel): ?>
                                        <button
                                            class="app-theme-switcher__option <?= $theme === $themeKey ? 'is-active' : '' ?>"
                                            type="submit"
                                            name="theme"
                                            value="<?= e($themeKey) ?>"
                                        >
                                            <span class="app-theme-switcher__preview app-theme-switcher__preview--<?= e($themeKey) ?>">
                                                <span class="app-theme-switcher__preview-top"></span>
                                                <span class="app-theme-switcher__preview-side"></span>
                                                <span class="app-theme-switcher__preview-body"></span>
                                            </span>
                                            <span class="app-theme-switcher__meta">
                                                <strong><?= e($themeLabel) ?></strong>
                                                <small><?= $theme === $themeKey ? 'Ativo' : 'Aplicar tema' ?></small>
                                            </span>
                                            <?php if ($theme === $themeKey): ?>
                                                <span class="app-theme-switcher__check" aria-hidden="true">
                                                    <svg viewBox="0 0 20 20"><path d="m5 10.5 3.1 3.1L15 6.7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                                                </span>
                                            <?php endif; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </form>
                            </div>
                        </div>
                        <a class="app-icon-button" href="<?= base_url('/panel/settings') ?>" aria-label="Configuracoes" title="Configuracoes">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m10.37 3.49-.22 1.86a6.97 6.97 0 0 0-1.57.91l-1.72-.74-1.5 2.6 1.5 1.1a7.5 7.5 0 0 0 0 1.82l-1.5 1.1 1.5 2.6 1.72-.74c.49.37 1.01.67 1.57.91l.22 1.86h3l.22-1.86c.56-.24 1.08-.54 1.57-.91l1.72.74 1.5-2.6-1.5-1.1a7.5 7.5 0 0 0 0-1.82l1.5-1.1-1.5-2.6-1.72.74a6.97 6.97 0 0 0-1.57-.91l-.22-1.86ZM12 9.25A2.75 2.75 0 1 1 12 14.75 2.75 2.75 0 0 1 12 9.25Z"></path></svg>
                        </a>
                        <div class="app-profile-menu">
                            <a class="app-profile-menu__trigger" href="<?= base_url('/panel/profile') ?>" aria-label="Abrir perfil">
                                <?php if ($userAvatar): ?>
                                    <img class="app-profile-menu__avatar app-profile-menu__avatar--image" src="<?= e($userAvatar) ?>" alt="Foto do usuario">
                                <?php else: ?>
                                    <span class="app-profile-menu__avatar"><?= e(strtoupper(substr((string) ($user['name'] ?? 'U'), 0, 1))) ?></span>
                                <?php endif; ?>
                                <span class="app-profile-menu__text">
                                    <strong><?= e($user['name'] ?? 'Usuario') ?></strong>
                                    <small><?= e($user['role'] ?? 'user') ?></small>
                                </span>
                                <svg class="app-profile-menu__caret" viewBox="0 0 20 20" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/></svg>
                            </a>
                            <div class="app-profile-menu__dropdown">
                                <a href="<?= base_url('/panel/profile') ?>">Perfil</a>
                                <a href="<?= base_url('/panel/settings') ?>">Configuracoes</a>
                                <form method="post" action="<?= base_url('/logout') ?>" class="app-profile-menu__form">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="app-profile-menu__logout">Sair</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="app-main">
                    <?php if ($message = flash('success')): ?>
                        <div class="alert alert-success"><?= e($message) ?></div>
                    <?php endif; ?>

                    <?php if ($message = flash('error')): ?>
                        <div class="alert alert-danger"><?= e($message) ?></div>
                    <?php endif; ?>

                    <?php require $viewFile; ?>
                </main>
            </div>
        </div>
    <?php else: ?>
        <?php if (!$hideNavbar): ?>
        <nav class="navbar navbar-expand-lg border-bottom nav-surface">
            <div class="container">
                <a class="navbar-brand brand-wordmark" href="<?= base_url('/dashboard') ?>"><?= e(config('app.name')) ?></a>
                <div class="ms-auto d-flex gap-2">
                    <?php if ($user): ?>
                        <a class="btn btn-outline-light btn-sm" href="<?= base_url('/tickets') ?>">Chamados</a>
                        <form method="post" action="<?= base_url('/logout') ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-warning btn-sm">Sair</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        <?php endif; ?>

        <main class="<?= $fullPage ? 'page-main page-main--full' : 'page-main container py-4' ?>">
            <?php if ($message = flash('success')): ?>
                <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>

            <?php if ($message = flash('error')): ?>
                <div class="alert alert-danger"><?= e($message) ?></div>
            <?php endif; ?>

            <?php require $viewFile; ?>
        </main>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($useSidebar): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shell = document.querySelector('[data-route-search]');
    if (!shell) return;

    const input = shell.querySelector('[data-route-search-input]');
    const links = Array.from(shell.querySelectorAll('[data-route-label]'));
    const dropdown = shell.querySelector('[data-route-search-results]');

    const filterRoutes = function () {
        const term = input.value.trim().toLowerCase();
        let visible = 0;

        links.forEach(function (link) {
            const haystack = link.getAttribute('data-route-label') || '';
            const match = term === '' || haystack.indexOf(term) !== -1;
            link.style.display = match ? 'grid' : 'none';
            if (match) visible++;
        });

        shell.classList.toggle('is-open', visible > 0 && document.activeElement === input);
    };

    input.addEventListener('focus', filterRoutes);
    input.addEventListener('input', filterRoutes);

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            shell.classList.remove('is-open');
            input.blur();
        }
    });

    document.addEventListener('click', function (event) {
        if (!shell.contains(event.target)) {
            shell.classList.remove('is-open');
        }
    });

    document.addEventListener('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            input.focus();
            input.select();
            filterRoutes();
        }
    });
});
</script>
<?php endif; ?>
</body>
</html>
