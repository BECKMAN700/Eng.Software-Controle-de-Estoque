<?php
$pageTitle = 'Relatorio de Giro de Estoque';
$pageSubtitle = 'Analise entradas, saidas e volume movimentado por produto com filtros refinados.';

$dadosGiro = $dadosGiro ?? [];
$categorias = $categorias ?? [];
$erros = $erros ?? [];

$busca = $_GET['busca'] ?? '';
$categoriaFilter = $_GET['categoria'] ?? '';
$dataInicial = $_GET['data_inicial'] ?? '';
$dataFinal = $_GET['data_final'] ?? '';
$situacaoFilter = $_GET['situacao'] ?? '';
$filtrosAplicados = $busca !== '' || $categoriaFilter !== '' || $dataInicial !== '' || $dataFinal !== '' || $situacaoFilter !== '';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

$buscaParams = http_build_query([
    'busca' => $busca,
    'categoria' => $categoriaFilter,
    'data_inicial' => $dataInicial,
    'data_final' => $dataFinal,
    'situacao' => $situacaoFilter
]);

ob_start();
?>

<section class="page-section">
    <article class="card filter-panel">
        <div class="card-header">
            <div>
                <h2>Filtros do giro</h2>
                <p>Use periodo, categoria e situacao para refinar a analise de movimentacao.</p>
            </div>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger" role="alert">
                Revise as datas informadas antes de consultar o relatorio.
            </div>
        <?php endif; ?>

        <form method="GET" action="index.php" class="report-filter-form">
            <input type="hidden" name="acao" value="giro_estoque">

            <div class="form-group">
                <label for="busca">Produto</label>
                <input type="text" id="busca" name="busca" value="<?= esc($busca) ?>" placeholder="Nome ou codigo">
            </div>

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

            <div class="form-group">
                <label for="data_inicial">Data inicial</label>
                <input type="date" id="data_inicial" name="data_inicial" value="<?= esc($dataInicial) ?>" class="<?= isset($erros['data_inicial']) ? 'field-invalid' : '' ?>">
                <?php if (isset($erros['data_inicial'])): ?>
                    <span class="form-error"><?= esc($erros['data_inicial']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="data_final">Data final</label>
                <input type="date" id="data_final" name="data_final" value="<?= esc($dataFinal) ?>" class="<?= isset($erros['data_final']) ? 'field-invalid' : '' ?>">
                <?php if (isset($erros['data_final'])): ?>
                    <span class="form-error"><?= esc($erros['data_final']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="situacao">Giro</label>
                <select id="situacao" name="situacao">
                    <option value="">Todos</option>
                    <option value="alto" <?= $situacaoFilter === 'alto' ? 'selected' : '' ?>>Alto giro</option>
                    <option value="medio" <?= $situacaoFilter === 'medio' ? 'selected' : '' ?>>Medio giro</option>
                    <option value="baixo" <?= $situacaoFilter === 'baixo' ? 'selected' : '' ?>>Baixo giro</option>
                </select>
            </div>

            <div class="form-actions report-filter-actions">
                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                <a href="index.php?acao=giro_estoque" class="btn btn-secondary">Limpar filtros</a>
            </div>
        </form>
    </article>
</section>

<section class="page-section">
    <article class="card">
        <div class="card-header">
            <div>
                <h2>Produtos mais e menos movimentados</h2>
                <p>
                    <?= $filtrosAplicados ? 'Resultado conforme os filtros selecionados.' : 'Total movimentado = entradas + saidas no historico geral.' ?>
                </p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=exportar-pdf&relatorio=giro_estoque&<?= $buscaParams ?>" target="_blank" class="btn btn-secondary">
                    Exportar PDF
                </a>
                <a href="index.php?acao=exportar-csv&relatorio=giro_estoque&<?= $buscaParams ?>" class="btn btn-secondary">
                    Exportar CSV
                </a>
            </div>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="empty-state">
                Corrija as datas para visualizar os resultados do giro de estoque.
            </div>
        <?php elseif (empty($dadosGiro)): ?>
            <div class="empty-state">
                Nenhum produto encontrado com os filtros informados.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th class="numeric">Entradas</th>
                            <th class="numeric">Saidas</th>
                            <th class="numeric">Total movimentado</th>
                            <th>Ultima movimentacao</th>
                            <th>Situacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dadosGiro as $row): ?>
                            <?php
                            $situacao = $row['situacao'];
                            if ($situacao === 'alto') {
                                $badgeClass = 'badge-success';
                                $label = 'Alto giro';
                            } elseif ($situacao === 'medio') {
                                $badgeClass = 'badge-warning';
                                $label = 'Medio giro';
                            } else {
                                $badgeClass = 'badge-danger';
                                $label = 'Baixo giro';
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($row['nome']) ?></div>
                                    <div class="product-code"><?= esc($row['codigo'] ?: 'Sem codigo') ?></div>
                                </td>
                                <td><?= esc($row['categoria'] ?: '-') ?></td>
                                <td class="numeric"><strong class="text-success"><?= (int) $row['total_entradas'] ?></strong></td>
                                <td class="numeric"><strong class="text-danger"><?= (int) $row['total_saidas'] ?></strong></td>
                                <td class="numeric"><strong><?= (int) $row['total_movimentado'] ?></strong></td>
                                <td><?= $row['ultima_movimentacao'] ? date('d/m/Y H:i', strtotime($row['ultima_movimentacao'])) : '-' ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $label ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
