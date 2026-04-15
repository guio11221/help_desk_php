<?php $title = 'Chamados'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="eyebrow mb-2">FILA</div>
        <h1 class="h3 mb-1">Lista de chamados</h1>
        <p class="text-secondary mb-0">Filtros rapidos para operacao diaria.</p>
    </div>
    <a class="btn btn-primary" href="<?= base_url('/tickets/create') ?>">Novo chamado</a>
</div>

<div class="card panel-card mb-3">
    <div class="card-body">
        <form method="get" action="<?= base_url('/tickets') ?>" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Codigo ou assunto" value="<?= e((string) $filters['search']) ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <?php foreach (['open', 'in_progress', 'resolved', 'closed'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select">
                    <option value="">Todas as prioridades</option>
                    <?php foreach (['low', 'medium', 'high', 'urgent'] as $priority): ?>
                        <option value="<?= e($priority) ?>" <?= $filters['priority'] === $priority ? 'selected' : '' ?>><?= e($priority) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card panel-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Codigo</th>
                <th>Assunto</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Prioridade</th>
                <th>Atualizado</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><a href="<?= base_url('/tickets/show?code=' . urlencode($ticket['code'])) ?>"><?= e($ticket['code']) ?></a></td>
                    <td><?= e($ticket['subject']) ?></td>
                    <td><?= e($ticket['category_name'] ?? '-') ?></td>
                    <td><?= e($ticket['status']) ?></td>
                    <td><?= e($ticket['priority']) ?></td>
                    <td><?= e($ticket['updated_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

