<?php $title = $ticket['code']; ?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="eyebrow mb-2"><?= e($ticket['code']) ?></div>
        <h1 class="h3 mb-1"><?= e($ticket['subject']) ?></h1>
        <p class="text-secondary mb-0">Detalhes completos do chamado e historico de interacoes.</p>
    </div>
    <span class="badge text-bg-primary"><?= e($ticket['status']) ?></span>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card panel-card mb-3">
            <div class="card-body">
                <h2 class="h5 mb-3">Descricao</h2>
                <p class="mb-0"><?= nl2br(e($ticket['description'])) ?></p>
            </div>
        </div>

        <div class="card panel-card">
            <div class="card-body">
                <h2 class="h5 mb-3">Comentarios</h2>

                <?php foreach ($comments as $comment): ?>
                    <div class="comment-block">
                        <div class="d-flex justify-content-between mb-1">
                            <strong><?= e($comment['author_name']) ?></strong>
                            <small class="text-secondary"><?= e($comment['created_at']) ?></small>
                        </div>
                        <p class="mb-1"><?= nl2br(e($comment['body'])) ?></p>
                        <?php if ((bool) $comment['is_internal']): ?>
                            <span class="badge text-bg-secondary">Interno</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <form method="post" action="<?= base_url('/tickets/comment') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="code" value="<?= e($ticket['code']) ?>">
                    <div class="mb-3">
                        <textarea name="body" class="form-control" rows="4" placeholder="Escreva um comentario" required></textarea>
                    </div>
                    <?php if (auth_role('agent') && current_user()['role'] !== 'requester'): ?>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="is_internal" value="1" id="is_internal">
                            <label for="is_internal" class="form-check-label">Comentario interno</label>
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-primary">Adicionar comentario</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card panel-card mb-3">
            <div class="card-body">
                <h3 class="h6 mb-3">Resumo</h3>
                <div class="detail-row"><span>Categoria</span><strong><?= e($ticket['category_name'] ?? '-') ?></strong></div>
                <div class="detail-row"><span>Prioridade</span><strong><?= e($ticket['priority']) ?></strong></div>
                <div class="detail-row"><span>Solicitante</span><strong><?= e($ticket['requester_name'] ?? '-') ?></strong></div>
                <div class="detail-row"><span>Responsavel</span><strong><?= e($ticket['agent_name'] ?? 'Nao atribuido') ?></strong></div>
                <div class="detail-row"><span>Criado</span><strong><?= e($ticket['created_at']) ?></strong></div>
            </div>
        </div>

        <?php if (auth_role('agent')): ?>
            <div class="card panel-card">
                <div class="card-body">
                    <h3 class="h6 mb-3">Atualizar status</h3>
                    <form method="post" action="<?= base_url('/tickets/status') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="code" value="<?= e($ticket['code']) ?>">
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <?php foreach (['open', 'in_progress', 'resolved', 'closed'] as $status): ?>
                                    <option value="<?= e($status) ?>" <?= $ticket['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="assigned_to" class="form-select">
                                <option value="">Manter responsavel</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?= (int) $agent['id'] ?>" <?= (int) ($ticket['assigned_to'] ?? 0) === (int) $agent['id'] ? 'selected' : '' ?>>
                                        <?= e($agent['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-outline-primary">Salvar</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

