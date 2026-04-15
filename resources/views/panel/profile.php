<?php
$user = $user ?? current_user();
$avatar = avatar_url($user);
$initial = strtoupper(substr((string) ($user['name'] ?? 'U'), 0, 1));
?>
<section class="panel-shell">
    <div class="panel-page-head">
        <div>
            <div class="eyebrow mb-2">Conta</div>
            <h2 class="panel-page-title">Perfil do usuario</h2>
            <p class="panel-page-subtitle">Atualize seus dados, senha e foto de perfil em um unico lugar.</p>
        </div>
    </div>

    <div class="panel-grid panel-grid--profile">
        <section class="panel-box panel-box--sticky">
            <div class="panel-profile panel-profile--stacked">
                <div class="panel-profile__avatar-wrap">
                    <?php if ($avatar): ?>
                        <img class="panel-profile__avatar-image" src="<?= e($avatar) ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <div class="panel-profile__avatar"><?= e($initial) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="panel-profile__name"><?= e($user['name'] ?? 'Usuario') ?></h3>
                    <p class="panel-profile__role"><?= e($user['role'] ?? 'user') ?></p>
                    <p class="panel-profile__meta"><?= e($user['email'] ?? '-') ?></p>
                </div>
            </div>

            <form method="post" action="<?= base_url('/panel/profile/avatar') ?>" enctype="multipart/form-data" class="panel-form panel-form--compact">
                <?= csrf_field() ?>
                <div class="panel-form__group">
                    <label class="form-label" for="avatar">Foto de perfil</label>
                    <input class="form-control" type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                    <div class="panel-form__hint">PNG, JPG, WEBP ou GIF. Limite de <?= (int) config('app.max_upload_mb', 10) ?> MB.</div>
                </div>
                <button class="btn btn-primary w-100" type="submit">Atualizar foto</button>
            </form>
        </section>

        <div class="panel-stack">
            <section class="panel-box">
                <div class="panel-section-head">
                    <div>
                        <h3 class="panel-box__title">Informacoes pessoais</h3>
                        <p class="panel-box__subtitle">Mantenha seus dados de contato atualizados.</p>
                    </div>
                    <span class="panel-tag"><?= e($user['role'] ?? 'user') ?></span>
                </div>

                <form method="post" action="<?= base_url('/panel/profile') ?>" class="panel-form">
                    <?= csrf_field() ?>
                    <div class="panel-form__grid">
                        <div class="panel-form__group">
                            <label class="form-label" for="name">Nome</label>
                            <input class="form-control" id="name" name="name" value="<?= e((string) old('name', $user['name'] ?? '')) ?>" required>
                        </div>
                        <div class="panel-form__group">
                            <label class="form-label" for="email">E-mail</label>
                            <input class="form-control" type="email" id="email" name="email" value="<?= e((string) old('email', $user['email'] ?? '')) ?>" required>
                        </div>
                        <div class="panel-form__group">
                            <label class="form-label" for="phone">Telefone</label>
                            <input class="form-control" id="phone" name="phone" value="<?= e((string) old('phone', $user['phone'] ?? '')) ?>" placeholder="(65) 99999-9999">
                        </div>
                    </div>
                    <div class="panel-form__actions">
                        <button class="btn btn-primary" type="submit">Salvar alteracoes</button>
                    </div>
                </form>
            </section>

            <section class="panel-box">
                <div class="panel-section-head">
                    <div>
                        <h3 class="panel-box__title">Seguranca</h3>
                        <p class="panel-box__subtitle">Troque sua senha com frequencia para proteger o acesso.</p>
                    </div>
                </div>

                <form method="post" action="<?= base_url('/panel/profile/password') ?>" class="panel-form">
                    <?= csrf_field() ?>
                    <div class="panel-form__grid">
                        <div class="panel-form__group">
                            <label class="form-label" for="current_password">Senha atual</label>
                            <input class="form-control" type="password" id="current_password" name="current_password" required>
                        </div>
                        <div class="panel-form__group">
                            <label class="form-label" for="new_password">Nova senha</label>
                            <input class="form-control" type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="panel-form__group">
                            <label class="form-label" for="new_password_confirmation">Confirmar nova senha</label>
                            <input class="form-control" type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                    </div>
                    <div class="panel-form__actions">
                        <button class="btn btn-outline-primary" type="submit">Atualizar senha</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</section>
