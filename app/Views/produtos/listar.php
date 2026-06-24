<?php
require_once __DIR__ . '/../../Helpers/Auth.php';
require_once __DIR__ . '/../partials/icons.php';
$pageTitle = 'Painel de estoque';
$pageSubtitle = 'Acompanhe produtos, alertas de estoque e movimentações principais.';

$produtos = $produtos ?? [];
$categorias = $categorias ?? [];
$unidades = $unidades ?? [];
$statusOptions = $statusOptions ?? [];
$erros = $erros ?? [];

$produtosAbaixoDoMinimo = $produtosAbaixoDoMinimo ?? [];
$produtosNoMinimo = $produtosNoMinimo ?? [];
$produtosAcimaDoMaximo = $produtosAcimaDoMaximo ?? [];

$busca = $busca ?? ($_GET['busca'] ?? '');
$categoria = $categoria ?? ($_GET['categoria'] ?? '');
$unidade = $unidade ?? ($_GET['unidade'] ?? '');
$status = $status ?? ($_GET['status'] ?? '');
$dataInicial = $dataInicial ?? ($_GET['data_inicial'] ?? '');
$dataFinal = $dataFinal ?? ($_GET['data_final'] ?? '');
$filtrosAtivos = $busca !== '' || $categoria !== '' || $unidade !== '' || $status !== '' || $dataInicial !== '' || $dataFinal !== '';

$totalProdutos = count($produtos);
$totalAbaixoMinimo = count($produtosAbaixoDoMinimo);
$totalNoMinimo = count($produtosNoMinimo);
$totalAcimaMaximo = count($produtosAcimaDoMaximo);

$totalUnidades = 0;

foreach ($produtos as $produto) {
    $totalUnidades += (int) ($produto['quantidade'] ?? 0);
}

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarDinheiro')) {
    function formatarDinheiro($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

if (!function_exists('formatarStatus')) {
    function formatarStatus($status): string
    {
        $status = strtolower((string) $status);

        if ($status === 'ativo') {
            return '<span class="badge badge-success">Ativo</span>';
        }

        if ($status === 'inativo') {
            return '<span class="badge badge-warning">Inativo</span>';
        }

        if ($status === 'descontinuado') {
            return '<span class="badge badge-danger">Descontinuado</span>';
        }

        return '<span class="badge badge-muted">' . esc($status ?: 'Não informado') . '</span>';
    }
}

if (!function_exists('situacaoEstoque')) {
    function situacaoEstoque(array $produto): array
    {
        $quantidadeAtual = (int) ($produto['quantidade'] ?? 0);
        $estoqueMinimo = (int) ($produto['estoque_minimo'] ?? 0);
        $estoqueMaximo = $produto['estoque_maximo'] ?? null;

        if ($estoqueMinimo > 0 && $quantidadeAtual < $estoqueMinimo) {
            return [
                'texto' => 'Crítico',
                'classe' => 'situacao-critico'
            ];
        }

        if ($estoqueMinimo > 0 && $quantidadeAtual === $estoqueMinimo) {
            return [
                'texto' => 'No mínimo',
                'classe' => 'situacao-minimo'
            ];
        }

        if ($estoqueMaximo !== null && $estoqueMaximo !== '' && $quantidadeAtual > (int) $estoqueMaximo) {
            return [
                'texto' => 'Acima do máximo',
                'classe' => 'situacao-maximo'
            ];
        }

        return [
            'texto' => 'Normal',
            'classe' => 'situacao-ok'
        ];
    }
}

ob_start();
?>

<section class="page-section">
    <div class="grid grid-4">
        <article class="metric-card">
            <p class="metric-label">Produtos encontrados</p>
            <strong class="metric-value"><?= $totalProdutos ?></strong>
            <p class="metric-description">Quantidade de produtos exibidos na listagem atual.</p>
        </article>

        <article class="metric-card summary-card-danger">
            <p class="metric-label">Abaixo do mínimo</p>
            <strong class="metric-value"><?= $totalAbaixoMinimo ?></strong>
            <p class="metric-description">Produtos que precisam de reabastecimento.</p>
        </article>

        <article class="metric-card summary-card-warning">
            <p class="metric-label">No estoque mínimo</p>
            <strong class="metric-value"><?= $totalNoMinimo ?></strong>
            <p class="metric-description">Produtos que chegaram exatamente no limite mínimo.</p>
        </article>

        <article class="metric-card summary-card-info">
            <p class="metric-label">Acima do máximo</p>
            <strong class="metric-value"><?= $totalAcimaMaximo ?></strong>
            <p class="metric-description">Produtos acima do limite máximo configurado.</p>
        </article>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Visão geral</h2>
                <p>Total de unidades em estoque considerando os produtos exibidos.</p>
            </div>

            <div class="dashboard-actions">

                 <?php if (Auth::isAdmin()): ?>

                 <a href="index.php?acao=criar" class="btn btn-primary">

                Cadastrar produto
          </a>

        <?php endif; ?>
        <a href="#produtos" class="btn btn-secondary">Ver produtos</a>

    <a href="#alertas-estoque" class="btn btn-secondary">
        Ver alertas
    </a>

    <a href="index.php?acao=divergencias" class="btn btn-secondary">
        Relatorio de limites
    </a>

</div>
            </div>
        </div>

        <div class="grid grid-3">
            <article class="metric-card">
                <p class="metric-label">Unidades em estoque</p>
                <strong class="metric-value"><?= $totalUnidades ?></strong>
                <p class="metric-description">Soma da quantidade atual dos produtos filtrados.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Categorias cadastradas</p>
                <strong class="metric-value"><?= count($categorias) ?></strong>
                <p class="metric-description">Categorias disponíveis para filtragem.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Unidades de medida</p>
                <strong class="metric-value"><?= count($unidades) ?></strong>
                <p class="metric-description">Tipos de unidade usados nos produtos.</p>
            </article>
        </div>
</section>

<section class="page-section" id="alertas-estoque">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Alertas de estoque</h2>
                <p>Acompanhe produtos abaixo do mínimo, no limite ou acima do máximo.</p>
            </div>
        </div>

        <?php if (empty($produtosAbaixoDoMinimo) && empty($produtosNoMinimo) && empty($produtosAcimaDoMaximo)): ?>
            <?= uiEmptyState('alert', 'Tudo sob controle', 'Nenhum produto abaixo do mínimo, no limite ou acima do máximo.') ?>
        <?php endif; ?>

        <?php if (!empty($produtosAbaixoDoMinimo)): ?>
            <div class="card summary-card-danger">
                <div class="card-header">
                    <div>
                        <h3>Produtos que precisam de reabastecimento</h3>
                        <p>Produtos com quantidade atual menor que o estoque mínimo.</p>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Código</th>
                                <th>Categoria</th>
                                <th class="numeric">Quantidade</th>
                                <th class="numeric">Mínimo</th>
                                <th class="numeric">Faltam</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtosAbaixoDoMinimo as $produto): ?>
                                <?php
                                    $quantidadeAtual = (int) ($produto['quantidade'] ?? 0);
                                    $estoqueMinimo = (int) ($produto['estoque_minimo'] ?? 0);
                                    $faltam = max(0, $estoqueMinimo - $quantidadeAtual);
                                ?>

                                <tr>
                                    <td>
                                        <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                        <div class="product-code"><?= esc($produto['unidade'] ?? 'Sem unidade') ?></div>
                                    </td>
                                    <td><?= esc($produto['codigo'] ?? 'Sem código') ?></td>
                                    <td><?= esc($produto['categoria'] ?? 'Sem categoria') ?></td>
                                    <td class="numeric">
                                        <span class="stock-pill situacao-critico">
                                            <?= $quantidadeAtual ?>
                                        </span>
                                    </td>
                                    <td class="numeric"><?= $estoqueMinimo ?></td>
                                    <td class="numeric">
                                        <?php if ($quantidadeAtual === 0): ?>
                                            <span class="badge badge-danger">Zerado</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= $faltam ?> un.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="index.php?acao=entrada&id=<?= (int) $produto['id'] ?>">
                                            Registrar entrada
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($produtosNoMinimo)): ?>
            <div class="card summary-card-warning">
                <div class="card-header">
                    <div>
                        <h3>Produtos no estoque mínimo</h3>
                        <p>Produtos que chegaram exatamente no limite mínimo configurado.</p>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Código</th>
                                <th>Categoria</th>
                                <th class="numeric">Quantidade</th>
                                <th class="numeric">Mínimo</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtosNoMinimo as $produto): ?>
                                <tr>
                                    <td>
                                        <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                        <div class="product-code"><?= esc($produto['unidade'] ?? 'Sem unidade') ?></div>
                                    </td>
                                    <td><?= esc($produto['codigo'] ?? 'Sem código') ?></td>
                                    <td><?= esc($produto['categoria'] ?? 'Sem categoria') ?></td>
                                    <td class="numeric">
                                        <span class="stock-pill situacao-minimo">
                                            <?= (int) ($produto['quantidade'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td class="numeric"><?= (int) ($produto['estoque_minimo'] ?? 0) ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="index.php?acao=entrada&id=<?= (int) $produto['id'] ?>">
                                            Registrar entrada
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($produtosAcimaDoMaximo)): ?>
            <div class="card summary-card-info">
                <div class="card-header">
                    <div>
                        <h3>Produtos acima do estoque máximo</h3>
                        <p>Produtos com quantidade atual maior que o limite máximo configurado.</p>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Código</th>
                                <th>Categoria</th>
                                <th class="numeric">Quantidade</th>
                                <th class="numeric">Máximo</th>
                                <th class="numeric">Excesso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtosAcimaDoMaximo as $produto): ?>
                                <?php
                                    $quantidadeAtual = (int) ($produto['quantidade'] ?? 0);
                                    $estoqueMaximo = (int) ($produto['estoque_maximo'] ?? 0);
                                    $excesso = max(0, $quantidadeAtual - $estoqueMaximo);
                                ?>

                                <tr>
                                    <td>
                                        <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                        <div class="product-code"><?= esc($produto['unidade'] ?? 'Sem unidade') ?></div>
                                    </td>
                                    <td><?= esc($produto['codigo'] ?? 'Sem código') ?></td>
                                    <td><?= esc($produto['categoria'] ?? 'Sem categoria') ?></td>
                                    <td class="numeric">
                                        <span class="stock-pill situacao-maximo">
                                            <?= $quantidadeAtual ?>
                                        </span>
                                    </td>
                                    <td class="numeric"><?= $estoqueMaximo ?></td>
                                    <td class="numeric">
                                        <span class="badge badge-muted"><?= $excesso ?> un.</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="page-section filters-card">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Filtros</h2>
                <p>Encontre produtos por nome, codigo, categoria, unidade, status ou periodo de cadastro.</p>
            </div>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger" role="alert">
                Revise as datas informadas para filtrar os produtos.
            </div>
        <?php elseif ($filtrosAtivos): ?>
            <div class="alert alert-success" role="alert">
                Filtros aplicados. A listagem abaixo mostra somente os produtos encontrados.
            </div>
        <?php endif; ?>

        <form class="filters-form" action="index.php" method="GET">
            <input type="hidden" name="acao" value="listar">

            <div class="form-group">
                <label for="busca">Buscar por nome ou código</label>
                <input
                    type="text"
                    id="busca"
                    name="busca"
                    placeholder="Ex: Arroz, P001..."
                    value="<?= esc($busca) ?>"
                >
            </div>

            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $itemCategoria): ?>
                        <option value="<?= esc($itemCategoria) ?>" <?= $categoria === $itemCategoria ? 'selected' : '' ?>>
                            <?= esc($itemCategoria) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="unidade">Unidade</label>
                <select id="unidade" name="unidade">
                    <option value="">Todas</option>
                    <?php foreach ($unidades as $itemUnidade): ?>
                        <option value="<?= esc($itemUnidade) ?>" <?= $unidade === $itemUnidade ? 'selected' : '' ?>>
                            <?= esc($itemUnidade) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Todos</option>
                    <?php foreach ($statusOptions as $itemStatus): ?>
                        <option value="<?= esc($itemStatus) ?>" <?= $status === $itemStatus ? 'selected' : '' ?>>
                            <?= esc(ucfirst($itemStatus)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="data_inicial">Data inicial</label>
                <input
                    type="date"
                    id="data_inicial"
                    name="data_inicial"
                    value="<?= esc($dataInicial) ?>"
                    class="<?= isset($erros['data_inicial']) ? 'field-invalid' : '' ?>"
                >
                <?php if (isset($erros['data_inicial'])): ?>
                    <span class="form-error"><?= esc($erros['data_inicial']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="data_final">Data final</label>
                <input
                    type="date"
                    id="data_final"
                    name="data_final"
                    value="<?= esc($dataFinal) ?>"
                    class="<?= isset($erros['data_final']) ? 'field-invalid' : '' ?>"
                >
                <?php if (isset($erros['data_final'])): ?>
                    <span class="form-error"><?= esc($erros['data_final']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="index.php?acao=listar" class="btn btn-secondary">Limpar filtros</a>
            </div>
        </form>
    </div>
</section>

<section class="page-section" id="produtos">
    <span id="movimentacoes" class="anchor-offset"></span>
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Produtos cadastrados</h2>
                <p>Lista completa dos produtos encontrados no sistema.</p>
            </div>

            <div class="card-header-actions">
                <button type="button" class="btn btn-secondary btn-sm" data-density-toggle data-density-target="#tabela-produtos" aria-pressed="false" title="Alternar densidade das linhas">
                    <?= uiIcon('list', 'btn-icon') ?>
                    <span data-density-label>Compactar</span>
                </button>

                <?php if (Auth::isAdmin()): ?>
                <a href="index.php?acao=criar" class="btn btn-primary">
                    Novo produto
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($produtos)): ?>
            <?php if (!empty($erros)): ?>
                <?= uiEmptyState('alert', 'Filtros de data inválidos', 'Corrija os filtros de data para visualizar os produtos.') ?>
            <?php elseif ($filtrosAtivos): ?>
                <?= uiEmptyState('search', 'Nenhum produto encontrado', 'Nenhum produto corresponde aos filtros aplicados. Tente limpar a busca.', 'Limpar filtros', 'index.php?acao=listar') ?>
            <?php else: ?>
                <?= uiEmptyState('box', 'Nenhum produto cadastrado', 'Comece adicionando seu primeiro produto ao estoque.', Auth::isAdmin() ? 'Cadastrar produto' : '', Auth::isAdmin() ? 'index.php?acao=criar' : '') ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table table--cards" id="tabela-produtos" data-table-enhanced data-page-size="10">
                    <thead>
                        <tr>
                            <th data-sort="text">Produto</th>
                            <th data-sort="text">Código</th>
                            <th data-sort="text">Categoria</th>
                            <th data-sort="text">Unidade</th>
                            <th>Status</th>
                            <th class="numeric" data-sort="num">Qtd.</th>
                            <th class="numeric" data-sort="num">Mín.</th>
                            <th class="numeric" data-sort="num">Máx.</th>
                            <th>Situação</th>
                            <th class="numeric" data-sort="num">Preço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <?php
                                $situacao = situacaoEstoque($produto);
                                $idProduto = (int) ($produto['id'] ?? 0);
                                $estoqueMaximo = $produto['estoque_maximo'] ?? null;
                            ?>

                            <tr>
                                <td data-label="Produto">
                                    <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                    <div class="product-code">
                                        ID #<?= $idProduto ?>
                                    </div>
                                </td>

                                <td data-label="Código"><?= esc($produto['codigo'] ?? 'Sem código') ?></td>
                                <td data-label="Categoria"><?= esc($produto['categoria'] ?? 'Sem categoria') ?></td>
                                <td data-label="Unidade"><?= esc($produto['unidade'] ?? '-') ?></td>
                                <td data-label="Status"><?= formatarStatus($produto['status'] ?? '') ?></td>

                                <td class="numeric" data-label="Qtd.">
                                    <span class="stock-pill <?= esc($situacao['classe']) ?>">
                                        <?= (int) ($produto['quantidade'] ?? 0) ?>
                                    </span>
                                </td>

                                <td class="numeric" data-label="Mín."><?= (int) ($produto['estoque_minimo'] ?? 0) ?></td>

                                <td class="numeric" data-label="Máx.">
                                    <?= ($estoqueMaximo !== null && $estoqueMaximo !== '') ? (int) $estoqueMaximo : '-' ?>
                                </td>

                                <?php
                                    $badgeSituacaoClasse = [
                                        'situacao-ok' => 'badge-success',
                                        'situacao-minimo' => 'badge-warning',
                                        'situacao-critico' => 'badge-danger',
                                        'situacao-maximo' => 'badge-muted',
                                    ][$situacao['classe']] ?? 'badge-muted';
                                ?>

                                <td data-label="Situação">
                                    <span class="badge <?= esc($badgeSituacaoClasse) ?>">
                                        <?= esc($situacao['texto']) ?>
                                    </span>
                                </td>

                                <td class="numeric" data-label="Preço"><?= formatarDinheiro($produto['preco'] ?? 0) ?></td>

                                <td class="col-actions" data-label="Ações">
                                    <div class="table-actions">
                                        <a class="btn btn-primary btn-sm" href="index.php?acao=movimentar&id=<?= $idProduto ?>">
                                            Movimentar
                                        </a>

                                        <div class="row-menu">
                                            <button type="button" class="btn btn-secondary btn-sm row-menu-btn" data-row-menu aria-haspopup="true" aria-expanded="false" aria-label="Mais ações">
                                                <?= uiIcon('more', 'btn-icon') ?>
                                            </button>

                                            <div class="row-menu-list" role="menu">
                                                <?php if (Auth::isAdmin()): ?>
                                                <a role="menuitem" href="index.php?acao=editar&id=<?= $idProduto ?>">Editar</a>
                                                <?php endif; ?>
                                                <a role="menuitem" href="index.php?acao=entrada&id=<?= $idProduto ?>">Entrada</a>
                                                <a role="menuitem" href="index.php?acao=saida&id=<?= $idProduto ?>">Saída</a>
                                                <a role="menuitem" href="index.php?acao=historico_movimentacoes&id=<?= $idProduto ?>">Histórico</a>
                                                <?php if (Auth::isAdmin()): ?>
                                                <form
                                                    class="inline-form"
                                                    action="index.php?acao=excluir"
                                                    method="POST"
                                                    data-confirm="Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita."
                                                    data-confirm-ok="Excluir"
                                                >
                                                    <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">
                                                    <input type="hidden" name="id" value="<?= $idProduto ?>">
                                                    <button type="submit" role="menuitem" class="row-menu-danger">Excluir</button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-pagination" data-pagination>
                <span class="table-pagination-info" data-page-info></span>
                <div class="table-pagination-controls">
                    <button type="button" class="btn btn-secondary btn-sm" data-page-prev>Anterior</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-page-next>Próximo</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
