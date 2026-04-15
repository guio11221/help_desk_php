<section class="login-shell">
    <div class="login-shell__brand">
        <div class="login-shell__brand-inner">
            <div class="login-brand-top">
                <div class="login-logo" aria-hidden="true">
                    <span class="login-logo__core"></span>
                    <span class="login-logo__ring"></span>
                </div>
                <div>
                    <div class="login-kicker">Help Desk</div>
                    <div class="login-brand-name">Painel de Atendimento</div>
                </div>
            </div>
             <p class="login-copy">
                Centralize chamados, acompanhe o historico e mantenha o time alinhado em uma unica plataforma.
            </p>
            <div class="login-brand-mark" aria-hidden="true">
                <div class="login-brand-mark__frame">
                    <div class="login-brand-mark__header">
                        <span class="login-brand-mark__badge"></span>
                        <span class="login-brand-mark__title">Workspace</span>
                    </div>
                    <div class="login-brand-mark__grid">
                        <div class="login-brand-mark__panel login-brand-mark__panel--large"></div>
                        <div class="login-brand-mark__panel"></div>
                        <div class="login-brand-mark__panel login-brand-mark__panel--accent"></div>
                        <div class="login-brand-mark__panel"></div>
                    </div>
                    <div class="login-brand-mark__line"></div>
                    <div class="login-brand-mark__line login-brand-mark__line--short"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-shell__form">
        <div class="login-form-card">
            <div class="login-form-card__top">
                <div class="login-kicker login-kicker--muted">Acesso seguro</div>
                <h2 class="login-form-card__title">Entrar na plataforma</h2>
                <p class="login-form-card__text">Informe seu e-mail e senha para acessar o painel.</p>
            </div>

            <form method="post" action="<?= base_url('/login') ?>" class="login-form">
                <?= csrf_field() ?>

                <div class="login-form__group">
                    <label class="form-label login-form__label">E-mail</label>
                    <input type="email" name="email" class="form-control form-control-lg login-form__input" placeholder="Informe seu login" value="<?= e((string) old('email')) ?>" required>
                </div>

                <div class="login-form__group">
                    <label class="form-label login-form__label">Senha</label>
                    <input type="password" name="password" class="form-control form-control-lg login-form__input" placeholder="Informe sua senha" required>
                </div>

                <button class="btn btn-primary btn-lg w-100 login-form__submit">Entrar</button>
            </form>

            <div class="login-form-card__foot">
                <span class="login-foot-chip">Acesso protegido</span>
                <span class="login-foot-chip">Sessao segura</span>
            </div>
        </div>
    </div>
</section>
