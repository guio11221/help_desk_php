<section class="panel-shell">
    <div class="panel-page-head">
        <div>
            <div class="eyebrow mb-2">Painel</div>
            <h2 class="panel-page-title">Configuracoes gerais</h2>
        </div>
    </div>

    <div class="panel-grid">
        <section class="panel-box">
            <h3 class="panel-box__title">Workspace</h3>
            <div class="panel-info-list">
                <div class="panel-info-list__row">
                    <span>Aplicacao</span>
                    <strong><?= e(config('app.name')) ?></strong>
                </div>
                <div class="panel-info-list__row">
                    <span>Ambiente</span>
                    <strong><?= e((string) config('app.env')) ?></strong>
                </div>
                <div class="panel-info-list__row">
                    <span>Timezone</span>
                    <strong>America/Cuiaba</strong>
                </div>
            </div>
        </section>

        <section class="panel-box">
            <h3 class="panel-box__title">Operacao</h3>
            <div class="panel-info-list">
                <div class="panel-info-list__row">
                    <span>Sessao segura</span>
                    <strong><?= config('app.session_secure') ? 'Ativa' : 'Desativada' ?></strong>
                </div>
                <div class="panel-info-list__row">
                    <span>Upload maximo</span>
                    <strong><?= (int) config('app.max_upload_mb', 10) ?> MB</strong>
                </div>
                <div class="panel-info-list__row">
                    <span>Diretorio de upload</span>
                    <strong><?= e((string) config('app.upload_dir')) ?></strong>
                </div>
            </div>
        </section>
    </div>
</section>

