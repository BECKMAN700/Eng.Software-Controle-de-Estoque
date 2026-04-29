<?php
$pageTitle = 'Painel de estoque';
$pageSubtitle = 'Acompanhe produtos, alertas de estoque e movimentações principais.';

$produtos = $produtos ?? [];
$categorias = $categorias ?? [];
$unidades = $unidades ?? [];
$statusOptions = $statusOptions ?? [];

$produtosAbaixoDoMinimo = $produtosAbaixoDoMinimo ?? [];
$produtosNoMinimo = $produtosNoMinimo ?? [];
$produtosAcimaDoMaximo = $produtosAcimaDoMaximo ?? [];

$busca = $busca ?? ($_GET['busca'] ?? '');
$categoria = $categoria ?? ($_GET['categoria'] ?? '');
$unidade = $unidade ?? ($_GET['unidade'] ?? '');
$status = $status ?? ($_GET['status'] ?? '');

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
                <a href="index.php?acao=criar" class="btn btn-primary">Cadastrar produto</a>
                <a href="#produtos" class="btn btn-secondary">Ver produtos</a>
                <a href="#alertas-estoque" class="btn btn-secondary">Ver alertas</a>
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
            <div class="empty-state">
                Nenhum alerta de estoque encontrado no momento.
            </div>
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
                                <th>Quantidade</th>
                                <th>Mínimo</th>
                                <th>Faltam</th>
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
                                    <td>
                                        <span class="stock-pill situacao-critico">
                                            <?= $quantidadeAtual ?>
                                        </span>
                                    </td>
                                    <td><?= $estoqueMinimo ?></td>
                                    <td>
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
                                <th>Quantidade</th>
                                <th>Mínimo</th>
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
                                    <td>
                                        <span class="stock-pill situacao-minimo">
                                            <?= (int) ($produto['quantidade'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td><?= (int) ($produto['estoque_minimo'] ?? 0) ?></td>
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
                                <th>Quantidade</th>
                                <th>Máximo</th>
                                <th>Excesso</th>
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
                                    <td>
                                        <span class="stock-pill situacao-maximo">
                                            <?= $quantidadeAtual ?>
                                        </span>
                                    </td>
                                    <td><?= $estoqueMaximo ?></td>
                                    <td>
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
                <p>Encontre produtos por nome, código, categoria, unidade ou status.</p>
            </div>
        </div>

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

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="index.php?acao=listar" class="btn btn-secondary">Limpar</a>
            </div>
        </form>
    </div>
</section>

<section class="page-section" id="produtos">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Produtos cadastrados</h2>
                <p>Lista completa dos produtos encontrados no sistema.</p>
            </div>

            <a href="index.php?acao=criar" class="btn btn-primary">
                Novo produto
            </a>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                Nenhum produto encontrado com os filtros informados.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th>Categoria</th>
                            <th>Unidade</th>
                            <th>Status</th>
                            <th>Qtd.</th>
                            <th>Mín.</th>
                            <th>Máx.</th>
                            <th>Situação</th>
                            <th>Preço</th>
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
                                <td>
                                    <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                    <div class="product-code">
                                        ID #<?= $idProduto ?>
                                    </div>
                                </td>

                                <td><?= esc($produto['codigo'] ?? 'Sem código') ?></td>
                                <td><?= esc($produto['categoria'] ?? 'Sem categoria') ?></td>
                                <td><?= esc($produto['unidade'] ?? '-') ?></td>
                                <td><?= formatarStatus($produto['status'] ?? '') ?></td>

                                <td>
                                    <span class="stock-pill <?= esc($situacao['classe']) ?>">
                                        <?= (int) ($produto['quantidade'] ?? 0) ?>
                                    </span>
                                </td>

                                <td><?= (int) ($produto['estoque_minimo'] ?? 0) ?></td>

                                <td>
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

                                <td>
                                    <span class="badge <?= esc($badgeSituacaoClasse) ?>">
                                        <?= esc($situacao['texto']) ?>
                                    </span>
                                </td>

                                <td><?= formatarDinheiro($produto['preco'] ?? 0) ?></td>

                                <td>
                                    <div class="table-actions">
                                        <a class="btn btn-secondary btn-sm" href="index.php?acao=editar&id=<?= $idProduto ?>">
                                            Editar
                                        </a>

                                        <a class="btn btn-primary btn-sm" href="index.php?acao=entrada&id=<?= $idProduto ?>">
                                            Entrada
                                        </a>

                                        <a class="btn btn-secondary btn-sm" href="index.php?acao=saida&id=<?= $idProduto ?>">
                                            Saída
                                        </a>

                                        <a class="btn btn-secondary btn-sm" href="index.php?acao=movimentar&id=<?= $idProduto ?>">
                                            Movimentar
                                        </a>

                                        <a class="btn btn-secondary btn-sm" href="index.php?acao=historico_movimentacoes&id=<?= $idProduto ?>">
                                            Histórico
                                        </a>

                                        <a
                                            class="btn btn-danger btn-sm"
                                            href="index.php?acao=excluir&id=<?= $idProduto ?>"
                                            onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                        >
                                            Excluir
                                        </a>
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

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';