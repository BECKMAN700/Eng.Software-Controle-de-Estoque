<?php
$totalProdutos = is_array($produtos ?? null) ? count($produtos) : 0;
$produtosBaixoEstoque = is_array($produtosAbaixoDoMinimo ?? null) ? count($produtosAbaixoDoMinimo) : 0;
$totalEntradas = 0;
$totalSaidas = 0;

foreach ($produtos ?? [] as $produtoItem) {
    foreach ($produtoItem['historico_movimentacoes'] ?? [] as $movimentacao) {
        $tipoMovimentacao = strtolower((string) ($movimentacao['tipo'] ?? ''));

        if ($tipoMovimentacao === 'entrada') {
            $totalEntradas += (int) ($movimentacao['quantidade'] ?? 0);
        }

        if ($tipoMovimentacao === 'saida') {
            $totalSaidas += (int) ($movimentacao['quantidade'] ?? 0);
        }
    }
}

$temFiltrosAtivos = (
    ($busca ?? '') !== '' ||
    ($categoria ?? '') !== '' ||
    ($unidade ?? '') !== '' ||
    ($status ?? '') !== ''
);

$statusMap = [
    'ativo' => 'Em estoque',
    'inativo' => 'Baixo',
    'descontinuado' => 'Esgotado'
];

$statusClassMap = [
    'ativo' => 'is-ok',
    'inativo' => 'is-low',
    'descontinuado' => 'is-empty'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="dashboard-page no-sidebar">
    <div class="app-shell">
        <div class="content-area">
            <header class="topbar">
                <div>
                    <h1>Controle de Estoque</h1>
                </div>

                <form class="searchbar" action="index.php" method="GET">
                    <input type="hidden" name="acao" value="listar">
                    <span class="search-icon" aria-hidden="true">⌕</span>
                    <input type="search" name="busca" value="<?= htmlspecialchars($busca ?? '') ?>" placeholder="Buscar produto ou código">
                </form>

                <button class="profile-button" type="button" aria-label="Perfil do usuário">
                    <span class="profile-avatar">U</span>
                    <span class="profile-meta">
                        <strong>Usuário</strong>
                        <small>Administrador</small>
                    </span>
                </button>
            </header>

            <main class="dashboard-grid">
                <section class="hero-card">
                    <div>
                        <p class="eyebrow">Visão geral</p>
                        <h2>Gestão clara e rápida.</h2>
                        <p>Monitore estoque, identifique produtos e mantenha o cadastro organizado e atualizado.</p>
                    </div>

                    <a class="primary-action" href="index.php?acao=criar">Adicionar Produto</a>
                </section>

                <section class="stats-grid" aria-label="Resumo do estoque">
                    <article class="stat-card">
                        <span class="stat-label">Total de produtos</span>
                        <strong><?= $totalProdutos ?></strong>
                        <small>Itens cadastrados e ativos no painel.</small>
                    </article>

                    <article class="stat-card warning">
                        <span class="stat-label">Produtos com estoque baixo</span>
                        <strong><?= $produtosBaixoEstoque ?></strong>
                        <small>Itens que pedem reposição imediata.</small>
                    </article>

                    <article class="stat-card success">
                        <span class="stat-label">Total de entradas</span>
                        <strong><?= $totalEntradas ?></strong>
                        <small>Movimentações de entrada registradas.</small>
                    </article>

                    <article class="stat-card danger">
                        <span class="stat-label">Total de saídas</span>
                        <strong><?= $totalSaidas ?></strong>
                        <small>Movimentações de saída registradas.</small>
                    </article>
                </section>

                <section class="panel panel-wide">
                    <div class="panel-header">
                        <div>
                            <p class="eyebrow">Catálogo</p>
                            <h3>Produtos cadastrados</h3>
                        </div>
                        <a class="secondary-action" href="index.php?acao=criar">Novo cadastro</a>
                    </div>

                    <form class="filter-bar" action="index.php" method="GET">
                        <input type="hidden" name="acao" value="listar">

                        <label class="filter-field">
                            <span>Categoria</span>
                            <select name="categoria">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $item): ?>
                                    <option value="<?= htmlspecialchars($item) ?>" <?= (($categoria ?? '') === $item) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($item) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="filter-field">
                            <span>Unidade</span>
                            <select name="unidade">
                                <option value="">Todas</option>
                                <?php foreach ($unidades as $item): ?>
                                    <option value="<?= htmlspecialchars($item) ?>" <?= (($unidade ?? '') === $item) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($item) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="filter-field">
                            <span>Status</span>
                            <select name="status">
                                <option value="">Todos</option>
                                <?php foreach ($statusOptions as $item): ?>
                                    <option value="<?= htmlspecialchars($item) ?>" <?= (($status ?? '') === $item) ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($item)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <div class="filter-actions">
                            <button class="primary-action compact" type="submit">Filtrar</button>
                            <a class="ghost-action" href="index.php?acao=listar">Limpar</a>
                        </div>
                    </form>

                    <?php if ($temFiltrosAtivos): ?>
                        <div class="filter-chip-row" aria-label="Filtros ativos">
                            <?php if (($busca ?? '') !== ''): ?>
                                <span class="chip">Busca: <?= htmlspecialchars($busca) ?></span>
                            <?php endif; ?>
                            <?php if (($categoria ?? '') !== ''): ?>
                                <span class="chip">Categoria: <?= htmlspecialchars($categoria) ?></span>
                            <?php endif; ?>
                            <?php if (($unidade ?? '') !== ''): ?>
                                <span class="chip">Unidade: <?= htmlspecialchars($unidade) ?></span>
                            <?php endif; ?>
                            <?php if (($status ?? '') !== ''): ?>
                                <span class="chip">Status: <?= htmlspecialchars($status) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-shell">
                        <?php if (empty($produtos)): ?>
                            <div class="empty-state">
                                <strong><?= $temFiltrosAtivos ? 'Nenhum produto encontrado.' : 'Nenhum produto cadastrado.' ?></strong>
                                <p>Use o botão de cadastro para começar ou ajuste os filtros para localizar itens específicos.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-scroll">
                                <table class="inventory-table">
                                    <thead>
                                        <tr>
                                            <th>Nome do produto</th>
                                            <th>Código</th>
                                            <th>Quantidade</th>
                                            <th>Categoria</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos as $index => $produto): ?>
                                            <?php
                                                $quantidade = (int) ($produto['quantidade'] ?? 0);
                                                $estoqueMinimo = (int) ($produto['estoque_minimo'] ?? 0);
                                                $situacao = $quantidade <= 0 ? 'descontinuado' : ($quantidade <= $estoqueMinimo ? 'inativo' : 'ativo');
                                                $statusTexto = $statusMap[$situacao] ?? 'Em estoque';
                                                $statusClasse = $statusClassMap[$situacao] ?? 'is-ok';
                                                $linhaAtiva = $index === 0 ? ' is-selected' : '';
                                            ?>
                                            <tr class="<?= $linhaAtiva ?>">
                                                <td>
                                                    <div class="product-cell">
                                                        <span class="selection-indicator" aria-hidden="true"></span>
                                                        <div>
                                                            <strong><?= htmlspecialchars($produto['nome'] ?? '') ?></strong>
                                                            <small>ID <?= (int) ($produto['id'] ?? 0) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($produto['codigo'] ?? '') ?></td>
                                                <td>
                                                    <span class="quantity-badge"><?= $quantidade ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($produto['categoria'] ?? '') ?></td>
                                                <td>
                                                    <span class="status-pill <?= $statusClasse ?>"><?= $statusTexto ?></span>
                                                </td>
                                                <td>
                                                    <div class="actions-group">
                                                        <a href="index.php?acao=editar&id=<?= (int) ($produto['id'] ?? 0) ?>">Editar</a>
                                                        <a href="index.php?acao=excluir&id=<?= (int) ($produto['id'] ?? 0) ?>" onclick="return confirm('Deseja excluir este produto?')">Excluir</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="panel form-panel">
                    <div class="panel-header">
                        <div>
                            <p class="eyebrow">Notificações</p>
                            <h3>Avisos do estoque</h3>
                        </div>
                        <span class="panel-tag">Produtos críticos</span>
                    </div>

                    <div class="notification-grid notification-grid-single">
                        <section class="notification-card warning-card">
                            <strong>Produtos com baixo estoque</strong>
                            <p>Itens que já estão abaixo do mínimo configurado.</p>
                            <?php if (empty($produtosAbaixoDoMinimo)): ?>
                                <span class="notification-empty">Nenhum alerta de baixo estoque no momento.</span>
                            <?php else: ?>
                                <ul class="notification-list">
                                    <?php foreach ($produtosAbaixoDoMinimo as $item): ?>
                                        <li>
                                            <span><?= htmlspecialchars($item['nome'] ?? '') ?></span>
                                            <small><?= (int) ($item['quantidade'] ?? 0) ?> / mín. <?= (int) ($item['estoque_minimo'] ?? 0) ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </section>

                        <section class="notification-card danger-card">
                            <strong>Produtos com alto estoque</strong>
                            <p>Itens que passaram do limite máximo configurado.</p>
                            <?php if (empty($produtosAcimaDoMaximo)): ?>
                                <span class="notification-empty">Nenhum alerta de alto estoque no momento.</span>
                            <?php else: ?>
                                <ul class="notification-list">
                                    <?php foreach ($produtosAcimaDoMaximo as $item): ?>
                                        <li>
                                            <span><?= htmlspecialchars($item['nome'] ?? '') ?></span>
                                            <small><?= (int) ($item['quantidade'] ?? 0) ?> / máx. <?= (int) ($item['estoque_maximo'] ?? 0) ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </section>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>