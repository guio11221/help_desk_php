<?php $title = 'Novo chamado'; ?>
<div class="row">
    <div class="col-xl-8">
        <div class="card panel-card">
            <div class="card-body p-4">
                <div class="eyebrow mb-2">ABERTURA</div>
                <h1 class="h4 mb-3">Criar chamado</h1>
                <form method="post" action="<?= base_url('/tickets/store') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Assunto</label>
                        <input type="text" name="subject" class="form-control" value="<?= e((string) old('subject')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descricao</label>
                        <textarea name="description" class="form-control" rows="6" required><?= e((string) old('description')) ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Categoria</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prioridade</label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Baixa</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Responsavel</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Sem atribuicao</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?= (int) $agent['id'] ?>"><?= e($agent['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Anexo</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Criar chamado</button>
                        <a class="btn btn-outline-secondary" href="<?= base_url('/tickets') ?>">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

