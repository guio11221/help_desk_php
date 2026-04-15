<?php
$totalTickets = array_sum($stats);
$activeTickets = (int) (($stats['open'] ?? 0) + ($stats['in_progress'] ?? 0));
$resolvedTickets = (int) (($stats['resolved'] ?? 0) + ($stats['closed'] ?? 0));
$resolutionRate = $totalTickets > 0 ? (int) round(($resolvedTickets / $totalTickets) * 100) : 0;
?>

<section class="dashboard-shell">
    <div class="dashboard-overview">
        <div>
            <div class="eyebrow mb-2">Resumo da operacao</div>
            <h2 class="dashboard-overview__title">Indicadores principais da central de atendimento.</h2>
            <p class="dashboard-overview__text">Visualize o volume atual da fila, a taxa de resolucao e acesse os ultimos chamados registrados.</p>
        </div>
        <div class="dashboard-overview__meta">
            <span class="dashboard-chip">Total <?= $totalTickets ?></span>
            <span class="dashboard-chip dashboard-chip--soft"><?= $resolutionRate ?>% resolvido</span>
        </div>
    </div>

    <div class="dashboard-stats">
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-card__label">Abertos</span>
            <strong class="dashboard-stat-card__value"><?= (int) ($stats['open'] ?? 0) ?></strong>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-card__label">Em andamento</span>
            <strong class="dashboard-stat-card__value"><?= (int) ($stats['in_progress'] ?? 0) ?></strong>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-card__label">Resolvidos</span>
            <strong class="dashboard-stat-card__value"><?= (int) ($stats['resolved'] ?? 0) ?></strong>
        </article>
        <article class="dashboard-stat-card">
            <span class="dashboard-stat-card__label">Fechados</span>
            <strong class="dashboard-stat-card__value"><?= (int) ($stats['closed'] ?? 0) ?></strong>
        </article>
    </div>

    <div class="dashboard-grid">
        <section class="dashboard-section dashboard-section--wide">
            <div class="dashboard-section__head">
                <div>
                    <div class="eyebrow mb-2">Fila recente</div>
                    <h2 class="dashboard-section__title">Ultimos chamados registrados</h2>
                </div>
                <a class="dashboard-section__link" href="<?= base_url('/tickets') ?>">Ver todos</a>
            </div>

            <div class="table-responsive">
                <table class="table dashboard-table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Assunto</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th>Atualizado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recent as $ticket): ?>
                        <tr>
                            <td>
                                <a class="dashboard-table__code" href="<?= base_url('/tickets/show?code=' . urlencode($ticket['code'])) ?>">
                                    <?= e($ticket['code']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="dashboard-table__subject"><?= e($ticket['subject']) ?></div>
                                <div class="dashboard-table__meta"><?= e($ticket['category_name'] ?? 'Sem categoria') ?></div>
                            </td>
                            <td><span class="status-pill status-pill--<?= e($ticket['status']) ?>"><?= e($ticket['status']) ?></span></td>
                            <td><span class="priority-pill priority-pill--<?= e($ticket['priority']) ?>"><?= e($ticket['priority']) ?></span></td>
                            <td><?= e($ticket['updated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="dashboard-stack">
            <section class="dashboard-section">
                <div class="dashboard-section__head dashboard-section__head--compact">
                    <div>
                        <div class="eyebrow mb-2">Indicadores</div>
                        <h2 class="dashboard-section__title">Panorama rapido</h2>
                    </div>
                </div>
                <div class="dashboard-summary">
                    <div class="dashboard-summary__row">
                        <span>Chamados ativos</span>
                        <strong><?= $activeTickets ?></strong>
                    </div>
                    <div class="dashboard-summary__row">
                        <span>Chamados resolvidos</span>
                        <strong><?= $resolvedTickets ?></strong>
                    </div>
                    <div class="dashboard-summary__row">
                        <span>Taxa de resolucao</span>
                        <strong><?= $resolutionRate ?>%</strong>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="dashboard-section__head dashboard-section__head--compact">
                    <div>
                        <div class="eyebrow mb-2">Acoes</div>
                        <h2 class="dashboard-section__title">Acessos rapidos</h2>
                    </div>
                </div>
                <div class="dashboard-links">
                    <a class="dashboard-link-card" href="<?= base_url('/tickets/create') ?>">
                        <strong>Abrir chamado</strong>
                        <span>Registrar uma nova solicitacao na fila.</span>
                    </a>
                    <a class="dashboard-link-card" href="<?= base_url('/tickets?status=open') ?>">
                        <strong>Ver abertos</strong>
                        <span>Focar no backlog que ainda nao entrou em execucao.</span>
                    </a>
                    <a class="dashboard-link-card" href="<?= base_url('/tickets?status=in_progress') ?>">
                        <strong>Ver em andamento</strong>
                        <span>Conferir o que ja esta em tratamento.</span>
                    </a>
                </div>
            </section>
        </aside>
    </div>
</section>
