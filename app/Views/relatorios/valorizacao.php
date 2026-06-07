<?php
$pageTitle = 'Relatório de Valorização do Estoque';
$pageSubtitle = 'Consulte o valor financeiro consolidado dos produtos ativos em estoque.';

$dadosValorizacao = $dadosValorizacao ?? [];
$categorias = $categorias ?? [];
$categoriaFilter = $_GET['categoria'] ?? '';
$valorTotalGeral = $valorTotalGeral ?? 0;
$totalItens = $totalItens ?? 0;

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
    <!-- Card de Filtros -->
    <article class="card" style="margin-bottom: 24px;">
        <form method="GET" action="index.php" style="margin: 0;">
            <input type="hidden" name="acao" value="valorizacao">
            
            <div style="display: grid; grid-template-columns: 2fr auto auto; gap: 16px; align-items: flex-end;">
                <div class="form-group" style="margin: 0;">
                    <label for="categoria" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Filtrar por categoria</label>
                    <select id="categoria" name="categoria" style="padding: 10px; border-radius: 8px; background-color: #fff;">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= esc($cat) ?>" <?= $categoriaFilter === $cat ? 'selected' : '' ?>>
                                <?= esc($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 20px;">
                        Filtrar
                    </button>
                    <a href="index.php?acao=valorizacao" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 15px;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </article>

    <!-- Resumo dos Valores -->
    <div class="grid grid-2" style="margin-bottom: 24px;">
        <article class="metric-card summary-card-info" style="border-left: 5px solid var(--primary);">
            <p class="metric-label" style="font-weight: 600;">Total de Unidades Físicas</p>
            <strong class="metric-value"><?= $totalItens ?></strong>
            <p class="metric-description">Soma de todas as quantidades dos produtos listados.</p>
        </article>

        <article class="metric-card summary-card-success" style="border-left: 5px solid var(--success);">
            <p class="metric-label" style="font-weight: 600;">Valor Total Financeiro</p>
            <strong class="metric-value" style="color: var(--success);"><?= formatarDinheiro($valorTotalGeral) ?></strong>
            <p class="metric-description">Soma da valorização (quantidade x preço unitário) de todos os itens.</p>
        </article>
    </div>

    <!-- Card de Dados -->
    <article class="card">
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 15px;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Valor de mercado do estoque atual</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">Apenas produtos com situação cadastral ativa são considerados.</p>
            </div>
            
            <div style="display: flex; gap: 8px;">
                <a href="index.php?acao=exportar-pdf&relatorio=valorizacao&<?= $buscaParams ?>" target="_blank" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid #dc2626; color: #dc2626; background: #fff;">
                     Exportar PDF
                </a>
                <a href="index.php?acao=exportar-csv&relatorio=valorizacao&<?= $buscaParams ?>" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid #16a34a; color: #16a34a; background: #fff;">
                     Exportar CSV
                </a>
            </div>
        </div>

        <?php if (empty($dadosValorizacao)): ?>
            <div class="empty-state">
                Nenhum produto cadastrado nesta categoria.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th style="text-align: center;">Categoria</th>
                            <th style="text-align: center;">Quantidade Atual</th>
                            <th style="text-align: right;">Preço Unitário</th>
                            <th style="text-align: right;">Valor em Estoque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dadosValorizacao as $row): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);"><?= esc($row['nome']) ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($row['codigo'] ?: 'Sem código') ?></div>
                                </td>
                                <td style="text-align: center; color: var(--text-muted);"><?= esc($row['categoria'] ?: '-') ?></td>
                                <td style="text-align: center; font-weight: 600;">
                                    <span class="stock-pill" style="padding: 4px 10px; background-color: #f1f5f9; border-radius: 6px; font-size: 0.9rem;">
                                        <?= $row['quantidade'] ?>
                                    </span>
                                </td>
                                <td style="text-align: right; color: var(--text-muted);"><?= formatarDinheiro($row['preco']) ?></td>
                                <td style="text-align: right; font-weight: 700; color: var(--text-main);"><?= formatarDinheiro($row['valor_total_produto']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Linha de Resumo do Rodapé da Tabela -->
                        <tr style="background-color: #f8fafc; font-weight: 800; border-top: 2px solid var(--border);">
                            <td colspan="2" style="padding: 16px 14px; color: var(--text-main);">TOTAL GERAL</td>
                            <td style="text-align: center; padding: 16px 14px; font-size: 1.1rem; color: var(--text-main);"><?= $totalItens ?></td>
                            <td style="text-align: right; padding: 16px 14px;">-</td>
                            <td style="text-align: right; padding: 16px 14px; font-size: 1.1rem; color: var(--success);"><?= formatarDinheiro($valorTotalGeral) ?></td>
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
