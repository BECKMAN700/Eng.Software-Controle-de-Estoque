<?php
$pageTitle = 'Catálogo de produtos';
$pageSubtitle = 'Visualize os produtos cadastrados em formato de cards.';

$produtos = $produtos ?? [];
$categorias = $categorias ?? [];
$unidades = $unidades ?? [];
$statusOptions = $statusOptions ?? [];

$busca = $busca ?? ($_GET['busca'] ?? '');
$categoria = $categoria ?? ($_GET['categoria'] ?? '');
$unidade = $unidade ?? ($_GET['unidade'] ?? '');
$status = $status ?? ($_GET['status'] ?? '');

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

if (!function_exists('statusProdutoBadge')) {
    function statusProdutoBadge($status): string
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

        return '<span class="badge badge-muted">Não informado</span>';
    }
}

if (!function_exists('situacaoCatalogo')) {
    function situacaoCatalogo(array $produto): array
    {
        $quantidade = (int) ($produto['quantidade'] ?? 0);
        $minimo = (int) ($produto['estoque_minimo'] ?? 0);
        $maximo = $produto['estoque_maximo'] ?? null;

        if ($minimo > 0 && $quantidade < $minimo) {
            return [
                'texto' => 'Abaixo do mínimo',
                'badge' => 'badge-danger',
                'card' => 'product-card-danger'
            ];
        }

        if ($minimo > 0 && $quantidade === $minimo) {
            return [
                'texto' => 'No mínimo',
                'badge' => 'badge-warning',
                'card' => 'product-card-warning'
            ];
        }

        if ($maximo !== null && $maximo !== '' && $quantidade > (int) $maximo) {
            return [
                'texto' => 'Acima do máximo',
                'badge' => 'badge-muted',
                'card' => 'product-card-info'
            ];
        }

        return [
            'texto' => 'Estoque normal',
            'badge' => 'badge-success',
            'card' => ''
        ];
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Catálogo de produtos</h2>
                <p>
                    Esta tela mostra os produtos em cards, ideal para uma visualização rápida e limpa.
                </p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=criar" class="btn btn-primary">
                    Novo produto
                </a>

                <a href="index.php?acao=listar" class="btn btn-secondary">
                    Ver tabela completa
                </a>
            </div>
        </div>

        <form class="filters-form" action="index.php" method="GET">
            <input type="hidden" name="acao" value="catalogo">

            <div class="form-group">
                <label for="busca">Buscar produto</label>
                <input
                    type="text"
                    id="busca"
                    name="busca"
                    placeholder="Digite nome ou código"
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
                <button type="submit" class="btn btn-primary">
                    Filtrar
                </button>

                <a href="index.php?acao=catalogo" class="btn btn-secondary">
                    Limpar
                </a>
            </div>
        </form>
    </div>
</section>

<section class="page-section">
    <?php if (empty($produtos)): ?>
        <div class="empty-state">
            Nenhum produto encontrado no catálogo.
        </div>
    <?php else: ?>
        <div class="product-card-grid">
            <?php foreach ($produtos as $produto): ?>
                <?php
                    $idProduto = (int) ($produto['id'] ?? 0);
                    $situacao = situacaoCatalogo($produto);
                    $quantidade = (int) ($produto['quantidade'] ?? 0);
                    $minimo = (int) ($produto['estoque_minimo'] ?? 0);
                    $maximo = $produto['estoque_maximo'] ?? null;
                ?>

                <article class="product-card <?= esc($situacao['card']) ?>">
                    <div class="product-card-header">
                        <div>
                            <h3 class="product-card-title">
                                <?= esc($produto['nome'] ?? 'Produto sem nome') ?>
                            </h3>

                            <p class="product-card-meta">
                                Código: <?= esc($produto['codigo'] ?? 'Sem código') ?>
                            </p>
                        </div>

                        <?= statusProdutoBadge($produto['status'] ?? '') ?>
                    </div>

                    <div class="product-card-body">
                        <div class="catalog-price">
                            <?= formatarDinheiro($produto['preco'] ?? 0) ?>
                        </div>

                        <div class="product-card-info-list">
                            <div>
                                <span>Categoria</span>
                                <strong><?= esc($produto['categoria'] ?? 'Sem categoria') ?></strong>
                            </div>

                            <div>
                                <span>Unidade</span>
                                <strong><?= esc($produto['unidade'] ?? '-') ?></strong>
                            </div>

                            <div>
                                <span>Quantidade</span>
                                <strong><?= $quantidade ?></strong>
                            </div>

                            <div>
                                <span>Estoque mínimo</span>
                                <strong><?= $minimo ?></strong>
                            </div>

                            <div>
                                <span>Estoque máximo</span>
                                <strong><?= ($maximo !== null && $maximo !== '') ? (int) $maximo : '-' ?></strong>
                            </div>
                        </div>

                        <div class="product-card-status">
                            <span class="badge <?= esc($situacao['badge']) ?>">
                                <?= esc($situacao['texto']) ?>
                            </span>
                        </div>

                        <?php if (!empty($produto['descricao'])): ?>
                            <p class="product-card-description">
                                <?= esc($produto['descricao']) ?>
                            </p>
                        <?php else: ?>
                            <p class="product-card-description text-muted">
                                Produto sem descrição cadastrada.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="product-card-footer">
                        <a class="btn btn-secondary btn-sm" href="index.php?acao=editar&id=<?= $idProduto ?>">
                            Editar
                        </a>

                        <a class="btn btn-primary btn-sm" href="index.php?acao=entrada&id=<?= $idProduto ?>">
                            Entrada
                        </a>

                        <a class="btn btn-secondary btn-sm" href="index.php?acao=saida&id=<?= $idProduto ?>">
                            Saída
                        </a>

                        <a class="btn btn-secondary btn-sm" href="index.php?acao=historico_movimentacoes&id=<?= $idProduto ?>">
                            Histórico
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';