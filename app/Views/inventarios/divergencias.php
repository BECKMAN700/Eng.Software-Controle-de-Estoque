<?php
$pageTitle = 'Relatório de Divergências';
$inventario = $inventario ?? [];
$itens = $itens ?? [];
$pageSubtitle = 'Comparativo entre quantidade do sistema e quantidade contada fisicamente.';

if (!function_exists('esc')) {
    function esc($valor) : string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <h2>Divergências - <?= esc($inventario['titulo'] ?? '') ?></h2>
            </div>
            <div class="dashboard-actions">
                <a href="index.php?acao=inventario_detalhar&id=<?= (int)($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    ← Voltar aos Detalhes
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Produtos com Divergência</h2>
        </div>

        <?php if (empty($itens)): ?>
            <div class="empty-state">Nenhum item encontrado.</div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th>Qtd. Sistema</th>
                            <th>Qtd. Contada</th>
                            <th>Divergência</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalFalta = 0;
                        $totalSobra = 0;
                        foreach ($itens as $item): 
                            $qtdSistema = (int)($item['quantidade_sistema'] ?? 0);
                            $qtdContada = $item['quantidade_contada'] !== null ? (int)$item['quantidade_contada'] : null;
                            $diferenca  = (int)($item['diferenca'] ?? 0);
                        ?>
                            <tr>
                                <td><strong><?= esc($item['produto_nome'] ?? '') ?></strong></td>
                                <td><?= esc($item['produto_codigo'] ?? '-') ?></td>
                                <td><?= $qtdSistema ?></td>
                                <td><?= $qtdContada ?? '<span class="text-muted">Não contada</span>' ?></td>
                                <td>
                                    <?php if ($diferenca > 0): ?>
                                        <span class="badge badge-success">+<?= $diferenca ?> (Sobra)</span>
                                        <?php $totalSobra += $diferenca; ?>
                                    <?php elseif ($diferenca < 0): ?>
                                        <span class="badge badge-danger"><?= $diferenca ?> (Falta)</span>
                                        <?php $totalFalta += abs($diferenca); ?>
                                    <?php else: ?>
                                        <span class="badge badge-info">0 (OK)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $diferenca !== 0 ? 'Divergente' : 'Conferido' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Resumo -->
            <div class="grid grid-3 mt-3">
                <article class="metric-card">
                    <p class="metric-label">Total de Sobras</p>
                    <strong class="metric-value text-success">+<?= $totalSobra ?></strong>
                </article>
                <article class="metric-card">
                    <p class="metric-label">Total de Faltas</p>
                    <strong class="metric-value text-danger">-<?= $totalFalta ?></strong>
                </article>
                <article class="metric-card">
                    <p class="metric-label">Itens com Divergência</p>
                    <strong class="metric-value"><?= count(array_filter($itens, fn($i) => ($i['diferenca'] ?? 0) != 0)) ?></strong>
                </article>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($inventario['status'] === 'em_conferencia' && Auth::isAdmin()): ?>
    <div class="card mt-3">
        <div class="card-header">
            <h3>Aprovar Ajustes de Estoque</h3>
        </div>
        <div class="p-3">
            <form action="index.php?acao=inventario_aprovar" method="POST">
                <input type="hidden" name="inventario_id" value="<?= (int)($inventario['id'] ?? 0) ?>">
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Tem certeza que deseja aprovar este inventário e atualizar o estoque?')">
                    ✅ Aprovar Ajustes e Finalizar Inventário
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';