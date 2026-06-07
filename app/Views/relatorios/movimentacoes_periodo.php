<?php
$pageTitle = 'Relatório de Movimentações por Período';
$pageSubtitle = 'Acompanhe as entradas e saídas ocorridas em um determinado intervalo de tempo.';

$movimentacoes = $movimentacoes ?? [];
$erros = $erros ?? [];
$categorias = $categorias ?? [];
$produtos = $produtos ?? [];
$totais = $totais ?? ['entrada' => 0, 'saida' => 0];

$dataInicial = $_GET['data_inicial'] ?? '';
$dataFinal = $_GET['data_final'] ?? '';
$tipoFilter = $_GET['tipo'] ?? 'todos';
$produtoFilter = $_GET['produto_id'] ?? '';
$categoriaFilter = $_GET['categoria'] ?? '';

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
            'devolucao' => 'Devolução',
            'transferencia' => 'Transferência',
            'venda' => 'Venda',
            'consumo_interno' => 'Consumo interno',
            'perda' => 'Perda',
            'avaria' => 'Avaria',
            'entrada_manual' => 'Entrada manual',
            'saida_manual' => 'Saída manual'
        ];
        return $motivos[$motivo] ?? ucfirst(str_replace('_', ' ', $motivo));
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
    <!-- Formulário de Filtros -->
    <article class="card" style="margin-bottom: 24px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; color: var(--text-main);">Filtros de Período</h3>
        
        <form method="GET" action="index.php" style="margin: 0;">
            <input type="hidden" name="acao" value="movimentacoes_periodo">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: flex-start;">
                <!-- Data Inicial (Obrigatório) -->
                <div class="form-group">
                    <label for="data_inicial" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">
                        Data Inicial <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="date" id="data_inicial" name="data_inicial" value="<?= esc($dataInicial) ?>" style="padding: 10px; border-radius: 8px; <?= isset($erros['data_inicial']) ? 'border-color: var(--danger);' : '' ?>">
                    <?php if (isset($erros['data_inicial'])): ?>
                        <span class="form-error" style="color: var(--danger); font-size: 0.8rem; margin-top: 4px; font-weight: bold;"><?= esc($erros['data_inicial']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Data Final (Obrigatório) -->
                <div class="form-group">
                    <label for="data_final" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">
                        Data Final <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="date" id="data_final" name="data_final" value="<?= esc($dataFinal) ?>" style="padding: 10px; border-radius: 8px; <?= isset($erros['data_final']) ? 'border-color: var(--danger);' : '' ?>">
                    <?php if (isset($erros['data_final'])): ?>
                        <span class="form-error" style="color: var(--danger); font-size: 0.8rem; margin-top: 4px; font-weight: bold;"><?= esc($erros['data_final']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tipo de Movimentação (Opcional) -->
                <div class="form-group">
                    <label for="tipo" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Tipo</label>
                    <select id="tipo" name="tipo" style="padding: 10px; border-radius: 8px; background-color: #fff;">
                        <option value="todos" <?= $tipoFilter === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="entrada" <?= $tipoFilter === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                        <option value="saida" <?= $tipoFilter === 'saida' ? 'selected' : '' ?>>Saída</option>
                    </select>
                </div>

                <!-- Produto (Opcional) -->
                <div class="form-group">
                    <label for="produto_id" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Produto</label>
                    <select id="produto_id" name="produto_id" style="padding: 10px; border-radius: 8px; background-color: #fff;">
                        <option value="">Todos os produtos</option>
                        <?php foreach ($produtos as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $produtoFilter == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['nome']) ?> <?= $p['codigo'] ? '(' . esc($p['codigo']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Categoria (Opcional) -->
                <div class="form-group">
                    <label for="categoria" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Categoria</label>
                    <select id="categoria" name="categoria" style="padding: 10px; border-radius: 8px; background-color: #fff;">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= esc($cat) ?>" <?= $categoriaFilter === $cat ? 'selected' : '' ?>>
                                <?= esc($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px;">
                <button type="submit" name="filtrar" value="1" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px;">
                    🔍 Consultar
                </button>
                <a href="index.php?acao=movimentacoes_periodo" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 18px;">
                    Limpar
                </a>
            </div>
        </form>
    </article>

    <?php if (isset($_GET['filtrar']) && empty($erros)): ?>
        <!-- Resumo do Período -->
        <div class="grid grid-2" style="margin-bottom: 24px;">
            <article class="metric-card summary-card-success" style="border-left: 5px solid var(--success);">
                <p class="metric-label" style="font-weight: 600;">Total de Entradas no Período</p>
                <strong class="metric-value"><?= $totais['entrada'] ?></strong>
                <p class="metric-description">Quantidade física de itens que entraram no estoque.</p>
            </article>

            <article class="metric-card summary-card-danger" style="border-left: 5px solid var(--danger);">
                <p class="metric-label" style="font-weight: 600;">Total de Saídas no Período</p>
                <strong class="metric-value" style="color: var(--danger);"><?= $totais['saida'] ?></strong>
                <p class="metric-description">Quantidade física de itens retirados/vendidos.</p>
            </article>
        </div>

        <!-- Tabela de Resultados -->
        <article class="card">
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 15px;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Movimentações localizadas</h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">Intervalo de <?= date('d/m/Y', strtotime($dataInicial)) ?> até <?= date('d/m/Y', strtotime($dataFinal)) ?>.</p>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <a href="index.php?acao=exportar-pdf&relatorio=movimentacoes&<?= $buscaParams ?>" target="_blank" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid #dc2626; color: #dc2626; background: #fff;">
                         Exportar PDF
                    </a>
                    <a href="index.php?acao=exportar-csv&relatorio=movimentacoes&<?= $buscaParams ?>" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid #16a34a; color: #16a34a; background: #fff;">
                         Exportar CSV
                    </a>
                </div>
            </div>

            <?php if (empty($movimentacoes)): ?>
                <div class="empty-state">
                    Nenhuma movimentação registrada neste período com os filtros informados.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Data/Hora</th>
                                <th>Produto</th>
                                <th style="text-align: center;">Tipo</th>
                                <th style="text-align: center;">Quantidade</th>
                                <th>Motivo</th>
                                <th>Responsável</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimentacoes as $row): ?>
                                <?php
                                $tipo = $row['tipo'];
                                $badgeClass = $tipo === 'entrada' ? 'badge-success' : 'badge-danger';
                                ?>
                                <tr>
                                    <td style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                                        <?= date('d/m/Y H:i', strtotime($row['data_hora'])) ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-main);"><?= esc($row['produto_nome']) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($row['produto_codigo'] ?: 'Sem código') ?></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge <?= $badgeClass ?>" style="font-size: 0.8rem; font-weight: bold; text-transform: uppercase;">
                                            <?= esc($row['tipo']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: var(--text-main);">
                                        <?= ($row['tipo'] === 'entrada' ? '+' : '-') . $row['quantidade'] ?>
                                    </td>
                                    <td>
                                        <div style="color: var(--text-main); font-weight: 600;">
                                            <?= esc(formatarMotivo($row['motivo'])) ?>
                                        </div>
                                        <?php if (!empty($row['observacao'])): ?>
                                            <div style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;" title="<?= esc($row['observacao']) ?>">
                                                Obs: <?= esc(mb_strimwidth($row['observacao'], 0, 40, '...')) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--text-muted); font-weight: 600;">
                                        <?= esc($row['usuario_nome'] ?: 'Sistema') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    <?php elseif (isset($_GET['filtrar']) && !empty($erros)): ?>
        <div class="alert alert-danger" style="margin-top: 24px;">
            Por favor, corrija os erros no formulário de datas acima.
        </div>
    <?php else: ?>
        <div class="empty-state" style="margin-top: 24px;">
            Informe as datas inicial e final para gerar a consulta de movimentações.
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
