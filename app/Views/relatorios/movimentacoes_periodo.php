<?php
$pageTitle = 'Relatorio de Movimentacoes por Periodo';
$pageSubtitle = 'Acompanhe entradas e saidas em um intervalo de datas com filtros reais do estoque.';

$movimentacoes = $movimentacoes ?? [];
$erros = $erros ?? [];
$categorias = $categorias ?? [];
$produtos = $produtos ?? [];
$totais = $totais ?? ['entrada' => 0, 'saida' => 0];
$realizarConsulta = $realizarConsulta ?? false;

$dataInicial = $_GET['data_inicial'] ?? '';
$dataFinal = $_GET['data_final'] ?? '';
$tipoFilter = $_GET['tipo'] ?? 'todos';
$produtoFilter = $_GET['produto_id'] ?? '';
$categoriaFilter = $_GET['categoria'] ?? '';
$consultaSolicitada = isset($_GET['filtrar']) || $dataInicial !== '' || $dataFinal !== '';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarMotivo')) {
    function formatarMotivo($motivo): string
    {
        $motivos = [
            'compra' => 'Compra',
            'devolucao' => 'Devolucao',
            'transferencia' => 'Transferencia',
            'venda' => 'Venda',
            'consumo_interno' => 'Consumo interno',
            'perda' => 'Perda',
            'avaria' => 'Avaria',
            'entrada_manual' => 'Entrada manual',
            'saida_manual' => 'Saida manual'
        ];

        return $motivos[$motivo] ?? ucfirst(str_replace('_', ' ', (string) $motivo));
    }
}

$buscaParams = http_build_query([
    'data_inicial' => $dataInicial,
    'data_final' => $dataFinal,
    'tipo' => $tipoFilter,
    'produto_id' => $produtoFilter,
    'categoria' => $categoriaFilter
]);

ob_start();
?>

<section class="page-section">
    <article class="card filter-panel">
        <div class="card-header">
            <div>
                <h2>Filtros da consulta</h2>
                <p>Informe o periodo obrigatorio e refine por tipo, produto ou categoria.</p>
            </div>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger" role="alert">
                As datas informadas precisam ser corrigidas para gerar o relatorio.
            </div>
        <?php endif; ?>

        <form method="GET" action="index.php" class="report-filter-form">
            <input type="hidden" name="acao" value="movimentacoes_periodo">

            <div class="form-group">
                <label for="data_inicial">Data inicial <span class="required">*</span></label>
                <input type="date" id="data_inicial" name="data_inicial" value="<?= esc($dataInicial) ?>" class="<?= isset($erros['data_inicial']) ? 'field-invalid' : '' ?>">
                <?php if (isset($erros['data_inicial'])): ?>
                    <span class="form-error"><?= esc($erros['data_inicial']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="data_final">Data final <span class="required">*</span></label>
                <input type="date" id="data_final" name="data_final" value="<?= esc($dataFinal) ?>" class="<?= isset($erros['data_final']) ? 'field-invalid' : '' ?>">
                <?php if (isset($erros['data_final'])): ?>
                    <span class="form-error"><?= esc($erros['data_final']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo">
                    <option value="todos" <?= $tipoFilter === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="entrada" <?= $tipoFilter === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                    <option value="saida" <?= $tipoFilter === 'saida' ? 'selected' : '' ?>>Saida</option>
                </select>
            </div>

            <div class="form-group">
                <label for="produto_id">Produto</label>
                <select id="produto_id" name="produto_id">
                    <option value="">Todos os produtos</option>
                    <?php foreach ($produtos as $p): ?>
                        <option value="<?= (int) $p['id'] ?>" <?= (string) $produtoFilter === (string) $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['nome']) ?><?= $p['codigo'] ? ' (' . esc($p['codigo']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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

            <div class="form-actions report-filter-actions">
                <button type="submit" name="filtrar" value="1" class="btn btn-primary">Consultar</button>
                <a href="index.php?acao=movimentacoes_periodo" class="btn btn-secondary">Limpar filtros</a>
            </div>
        </form>
    </article>
</section>

<?php if ($consultaSolicitada && empty($erros)): ?>
    <section class="page-section">
        <div class="grid grid-2">
            <article class="metric-card summary-card-success">
                <p class="metric-label">Entradas no periodo</p>
                <strong class="metric-value"><?= (int) $totais['entrada'] ?></strong>
                <p class="metric-description">Quantidade fisica registrada como entrada.</p>
            </article>

            <article class="metric-card summary-card-danger">
                <p class="metric-label">Saidas no periodo</p>
                <strong class="metric-value"><?= (int) $totais['saida'] ?></strong>
                <p class="metric-description">Quantidade fisica registrada como saida.</p>
            </article>
        </div>
    </section>

    <section class="page-section">
        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Movimentacoes localizadas</h2>
                    <p>Intervalo de <?= date('d/m/Y', strtotime($dataInicial)) ?> ate <?= date('d/m/Y', strtotime($dataFinal)) ?>.</p>
                </div>

                <div class="dashboard-actions">
                    <a href="index.php?acao=exportar-pdf&relatorio=movimentacoes&<?= $buscaParams ?>" target="_blank" class="btn btn-secondary">
                        Exportar PDF
                    </a>
                    <a href="index.php?acao=exportar-csv&relatorio=movimentacoes&<?= $buscaParams ?>" class="btn btn-secondary">
                        Exportar CSV
                    </a>
                </div>
            </div>

            <?php if (empty($movimentacoes)): ?>
                <div class="empty-state">
                    Nenhuma movimentacao foi encontrada para o periodo e filtros selecionados.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Motivo</th>
                                <th>Responsavel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimentacoes as $row): ?>
                                <?php
                                $tipo = $row['tipo'];
                                $badgeClass = $tipo === 'entrada' ? 'badge-success' : 'badge-danger';
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['data_hora'])) ?></td>
                                    <td>
                                        <div class="product-name"><?= esc($row['produto_nome']) ?></div>
                                        <div class="product-code"><?= esc($row['produto_codigo'] ?: 'Sem codigo') ?></div>
                                    </td>
                                    <td><?= esc($row['produto_categoria'] ?: '-') ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= esc($row['tipo']) ?></span></td>
                                    <td><strong><?= ($row['tipo'] === 'entrada' ? '+' : '-') . (int) $row['quantidade'] ?></strong></td>
                                    <td>
                                        <div><?= esc(formatarMotivo($row['motivo'])) ?></div>
                                        <?php if (!empty($row['observacao'])): ?>
                                            <div class="product-code"><?= esc(mb_strimwidth($row['observacao'], 0, 42, '...')) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($row['usuario_nome'] ?: 'Sistema') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    </section>
<?php elseif ($consultaSolicitada && !empty($erros)): ?>
    <section class="page-section">
        <div class="empty-state">
            Corrija as datas destacadas para consultar as movimentacoes.
        </div>
    </section>
<?php else: ?>
    <section class="page-section">
        <div class="empty-state">
            Informe data inicial e data final para visualizar as movimentacoes do periodo.
        </div>
    </section>
<?php endif; ?>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
