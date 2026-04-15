<section class="panel-shell">
    <div class="panel-page-head">
        <div>
            <div class="eyebrow mb-2">Aparencia</div>
            <h2 class="panel-page-title">Temas do painel</h2>
        </div>
    </div>

    <form method="post" action="<?= base_url('/panel/theme') ?>" class="panel-theme-grid">
        <?= csrf_field() ?>
        <?php foreach ([
            'business' => ['title' => 'Business', 'desc' => 'Visual claro e administrativo.'],
            'graphite' => ['title' => 'Graphite', 'desc' => 'Painel escuro e discreto.'],
            'forest' => ['title' => 'Forest', 'desc' => 'Tom verde corporativo.'],
        ] as $key => $theme): ?>
            <label class="theme-card <?= $currentTheme === $key ? 'is-active' : '' ?>">
                <input type="radio" name="theme" value="<?= e($key) ?>" <?= $currentTheme === $key ? 'checked' : '' ?>>
                <span class="theme-card__title"><?= e($theme['title']) ?></span>
                <span class="theme-card__desc"><?= e($theme['desc']) ?></span>
            </label>
        <?php endforeach; ?>

        <div class="panel-theme-actions">
            <button class="btn btn-primary" type="submit">Salvar tema</button>
        </div>
    </form>
</section>

