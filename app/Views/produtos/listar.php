<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque</title>
    <link rel="stylesheet" href="../public/estoque.css">
</head>
<body>
<div class="page-wrapper">

    <div class="page-header">
        <div>
            <h1 class="page-title">Controle de <span>Estoque</span></h1>
            <p class="page-subtitle">Gerencie produtos, entradas, saídas e movimentações</p>
        </div>
        <a href="index.php?acao=criar" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Produto
        </a>
    </div>

    <?php if (!empty($produtosAbaixoDoMinimo)): ?>
    <details class="alert-restock" open>
        <summary>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f0a500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Produtos que precisam de reabastecimento
            <span class="alert-count"><?= count($produtosAbaixoDoMinimo) ?></span>
            <small style="color:var(--text-muted);font-weight:400;font-size:.78rem;">mínimo: <?= (int)$estoqueMinimo ?> un.</small>
            <span class="alert-chevron">▼</span>
        </summary>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>Produto</th><th>Código</th><th>Categoria</th><th>Unidade</th><th>Qtd. Atual</th><th>Ação</th></tr></thead>
                <tbody>
                    <?php foreach ($produtosAbaixoDoMinimo as $item): ?>
                    <?php $q = (int)$item['quantidade']; ?>
                    <tr>
                        <td style="font-weight:500"><?= htmlspecialchars($item['nome']) ?></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars($item['codigo'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($item['categoria'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($item['unidade'] ?? '—') ?></td>
                        <td><span class="qty-badge <?= $q === 0 ? 'qty-zero' : 'qty-low' ?>"><?= $q === 0 ? '⛔ Zerado' : $q ?></span></td>
                        <td><a href="index.php?acao=entrada&id=<?= (int)$item['id'] ?>" class="btn btn-ghost btn-sm">+ Registrar entrada</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>
    <?php endif; ?>

    <div class="filters-card">
        <form action="index.php" method="GET">
            <input type="hidden" name="acao" value="listar">
            <div class="filters-grid">
                <div class="form-group" style="margin:0">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control" placeholder="Nome ou código…" value="<?= htmlspecialchars($busca ?? '') ?>">
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $item): ?>
                        <option value="<?= htmlspecialchars($item) ?>" <?= (($categoria??'') === $item) ? 'selected' : '' ?>><?= htmlspecialchars($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Unidade</label>
                    <select name="unidade" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($unidades as $item): ?>
                        <option value="<?= htmlspecialchars($item) ?>" <?= (($unidade??'') === $item) ? 'selected' : '' ?>><?= htmlspecialchars($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($statusOptions as $item): ?>
                        <option value="<?= htmlspecialchars($item) ?>" <?= (($status??'') === $item) ? 'selected' : '' ?>><?= ucfirst(htmlspecialchars($item)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex;gap:8px;align-items:flex-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="index.php?acao=listar" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>
    </div>

    <?php $temFiltrosAtivos = (($busca??'')!==''||($categoria??'')!==''||($unidade??'')!==''||($status??'')!==''); ?>

    <?php if (empty($produtos)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="icon">📦</div>
            <h3><?= $temFiltrosAtivos ? 'Nenhum resultado encontrado' : 'Nenhum produto cadastrado' ?></h3>
            <p><?= $temFiltrosAtivos ? 'Tente ajustar os filtros acima.' : 'Comece cadastrando seu primeiro produto.' ?></p>
            <?php if (!$temFiltrosAtivos): ?><br><a href="index.php?acao=criar" class="btn btn-primary" style="margin-top:4px">Cadastrar produto</a><?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Produto</th><th>Código</th><th>Categoria</th><th>Unidade</th><th>Status</th><th>Quantidade</th><th>Preço</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <?php $q = (int)($produto['quantidade'] ?? 0); ?>
                <tr>
                    <td style="color:var(--text-dim);font-size:.8rem"><?= $produto['id'] ?></td>
                    <td style="font-weight:500"><?= htmlspecialchars($produto['nome'] ?? '') ?></td>
                    <td style="color:var(--text-muted);font-size:.83rem"><?= htmlspecialchars($produto['codigo'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($produto['categoria'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($produto['unidade'] ?? '—') ?></td>
                    <td><?php $st = $produto['status'] ?? 'ativo'; ?><span class="badge badge-<?= htmlspecialchars($st) ?>"><?= ucfirst(htmlspecialchars($st)) ?></span></td>
                    <td><span class="qty-badge <?= $q===0 ? 'qty-zero' : ($q<10 ? 'qty-low' : 'qty-normal') ?>"><?= $q ?></span></td>
                    <td style="font-variant-numeric:tabular-nums">R$ <?= number_format((float)($produto['preco']??0),2,',','.') ?></td>
                    <td>
                        <div class="actions-cell">
                            <a href="index.php?acao=entrada&id=<?= $produto['id'] ?>" class="btn btn-ghost btn-sm">↑ Entrada</a>
                            <a href="index.php?acao=saida&id=<?= $produto['id'] ?>" class="btn btn-ghost btn-sm" style="color:var(--danger);border-color:rgba(224,82,82,.25)">↓ Saída</a>
                            <a href="index.php?acao=historico_movimentacoes&id=<?= $produto['id'] ?>" class="btn btn-secondary btn-sm">Histórico</a>
                            <a href="index.php?acao=editar&id=<?= $produto['id'] ?>" class="btn btn-secondary btn-sm">Editar</a>
                            <a href="index.php?acao=excluir&id=<?= $produto['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir este produto?')">Excluir</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>
</body>
</html>