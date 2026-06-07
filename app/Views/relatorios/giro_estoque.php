<?php
$pageTitle = 'Relatório de Giro de Estoque';
$pageSubtitle = 'Acompanhe o ritmo de movimentação (entradas e saídas) de cada item do estoque.';

$dadosGiro = $dadosGiro ?? [];
$busca = $_GET['busca'] ?? '';
$situacaoFilter = $_GET['situacao'] ?? '';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

$buscaParams = http_build_query([
    'busca' => $busca,
    'situacao' => $situacaoFilter
]);

ob_start();
?>

<section class="page-section">
    <!-- Card de Filtros -->
    <article class="card" style="margin-bottom: 24px;">
        <form method="GET" action="index.php" style="margin: 0;">
            <input type="hidden" name="acao" value="giro_estoque">
            
            <div style="display: grid; grid-template-columns: 2fr 1fr auto auto; gap: 16px; align-items: flex-end;">
                <div class="form-group" style="margin: 0;">
                    <label for="busca" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Pesquisar produto</label>
                    <input type="text" id="busca" name="busca" value="<?= esc($busca) ?>" placeholder="Nome ou código do produto..." style="padding: 10px; border-radius: 8px;">
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <label for="situacao" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Giro de Estoque</label>
                    <select id="situacao" name="situacao" style="padding: 10px; border-radius: 8px; background-color: #fff;">
                        <option value="">Todos</option>
                        <option value="alto" <?= $situacaoFilter === 'alto' ? 'selected' : '' ?>>Alto Giro (>= 50 mov.)</option>
                        <option value="medio" <?= $situacaoFilter === 'medio' ? 'selected' : '' ?>>Médio Giro (> 10 e < 50 mov.)</option>
                        <option value="baixo" <?= $situacaoFilter === 'baixo' ? 'selected' : '' ?>>Baixo Giro (<= 10 mov.)</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 20px;">
                        Filtrar
                    </button>
                    <a href="index.php?acao=giro_estoque" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 15px;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </article>

    <!-- Card de Dados -->
    <article class="card">
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 15px;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Produtos mais e menos movimentados</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">Total movimentado = entradas + saídas no histórico geral.</p>
            </div>
            
            <div style="display: flex; gap: 8px;">
                <!-- Links de Exportação baseados nos filtros ativos -->
                <a href="index.php?acao=exportar-pdf&relatorio=giro_estoque&<?= $buscaParams ?>" target="_blank" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid #dc2626; color: #dc2626; background: #fff;">
                     Exportar PDF
                </a>
                <a href="index.php?acao=exportar-csv&relatorio=giro_estoque&<?= $buscaParams ?>" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid #16a34a; color: #16a34a; background: #fff;">
                     Exportar CSV
                </a>
            </div>
        </div>

        <?php if (empty($dadosGiro)): ?>
            <div class="empty-state">
                Nenhum produto encontrado com os filtros selecionados.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th style="text-align: center;">Categoria</th>
                            <th style="text-align: center;">Entradas</th>
                            <th style="text-align: center;">Saídas</th>
                            <th style="text-align: center;">Total Movimentado</th>
                            <th style="text-align: center;">Última Movimentação</th>
                            <th style="text-align: center;">Situação (Giro)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dadosGiro as $row): ?>
                            <?php
                            $situacao = $row['situacao'];
                            if ($situacao === 'alto') {
                                $badgeClass = 'badge-success';
                                $label = 'Alto Giro';
                            } elseif ($situacao === 'medio') {
                                $badgeClass = 'badge-warning';
                                $label = 'Médio Giro';
                            } else {
                                $badgeClass = 'badge-danger';
                                $label = 'Baixo Giro';
                            }
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);"><?= esc($row['nome']) ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($row['codigo'] ?: 'Sem código') ?></div>
                                </td>
                                <td style="text-align: center; color: var(--text-muted);"><?= esc($row['categoria'] ?: '-') ?></td>
                                <td style="text-align: center; font-weight: 600; color: var(--success);"><?= $row['total_entradas'] ?></td>
                                <td style="text-align: center; font-weight: 600; color: var(--danger);"><?= $row['total_saidas'] ?></td>
                                <td style="text-align: center; font-weight: 700; color: var(--text-main);"><?= $row['total_movimentado'] ?></td>
                                <td style="text-align: center; font-size: 0.9rem; color: var(--text-muted);">
                                    <?= $row['ultima_movimentacao'] ? date('d/m/Y H:i', strtotime($row['ultima_movimentacao'])) : '-' ?>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge <?= $badgeClass ?>" style="font-size: 0.8rem; font-weight: bold;">
                                        <?= $label ?>
                                    </span>
                                </td>
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
