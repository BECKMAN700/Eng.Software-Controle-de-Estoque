<?php
$pageTitle = 'Relatorio de Valorizacao do Estoque';
$pageSubtitle = 'Consulte o valor financeiro dos produtos ativos em estoque por categoria.';

$dadosValorizacao = $dadosValorizacao ?? [];
$categorias = $categorias ?? [];
$categoriaFilter = $_GET['categoria'] ?? '';
$valorTotalGeral = $valorTotalGeral ?? 0;
$totalItens = $totalItens ?? 0;
$filtrosAplicados = $categoriaFilter !== '';

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

$buscaParams = http_build_query([
    'categoria' => $categoriaFilter
]);

ob_start();
?>

<section class="page-section">
    <article class="card filter-panel">
        <div class="card-header">
            <div>
                <h2>Filtro de valorizacao</h2>
                <p>Refine a consulta usando as categorias cadastradas nos produtos.</p>
            </div>
        </div>

        <form method="GET" action="index.php" class="report-filter-form report-filter-form-compact">
            <input type="hidden" name="acao" value="valorizacao">

            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= esc($cat) ?>" <?= $categoriaFilter === $cat ? 'selected' : '' ?>>
                            <?= esc($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions report-filter-actions">
                <button type="submit" class="btn btn-primary">Aplicar filtro</button>
                <a href="index.php?acao=valorizacao" class="btn btn-secondary">Limpar filtros</a>
            </div>
        </form>
    </article>
</section>

<section class="page-section">
    <div class="grid grid-2">
        <article class="metric-card summary-card-info">
            <p class="metric-label">Total de unidades fisicas</p>
            <strong class="metric-value"><?= (int) $totalItens ?></strong>
            <p class="metric-description">Soma das quantidades dos produtos listados.</p>
        </article>

        <article class="metric-card summary-card-success">
            <p class="metric-label">Valor total financeiro</p>
            <strong class="metric-value"><?= formatarDinheiro($valorTotalGeral) ?></strong>
            <p class="metric-description">Quantidade atual multiplicada pelo preco unitario.</p>
        </article>
    </div>
</section>

<section class="page-section">
    <article class="card">
        <div class="card-header">
            <div>
                <h2>Valor de mercado do estoque atual</h2>
                <p>
                    <?= $filtrosAplicados ? 'Resultado filtrado pela categoria selecionada.' : 'Apenas produtos ativos sao considerados.' ?>
                </p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=exportar-pdf&relatorio=valorizacao&<?= $buscaParams ?>" target="_blank" class="btn btn-secondary">
                    Exportar PDF
                </a>
                <a href="index.php?acao=exportar-csv&relatorio=valorizacao&<?= $buscaParams ?>" class="btn btn-secondary">
                    Exportar CSV
                </a>
            </div>
        </div>

        <?php if (empty($dadosValorizacao)): ?>
            <div class="empty-state">
                Nenhum produto ativo foi encontrado para a categoria selecionada.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Quantidade atual</th>
                            <th>Preco unitario</th>
                            <th>Valor em estoque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dadosValorizacao as $row): ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($row['nome']) ?></div>
                                    <div class="product-code"><?= esc($row['codigo'] ?: 'Sem codigo') ?></div>
                                </td>
                                <td><?= esc($row['categoria'] ?: '-') ?></td>
                                <td><span class="stock-pill"><?= (int) $row['quantidade'] ?></span></td>
                                <td><?= formatarDinheiro($row['preco']) ?></td>
                                <td><strong><?= formatarDinheiro($row['valor_total_produto']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-total-row">
                            <td colspan="2">TOTAL GERAL</td>
                            <td><?= (int) $totalItens ?></td>
                            <td>-</td>
                            <td><?= formatarDinheiro($valorTotalGeral) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
