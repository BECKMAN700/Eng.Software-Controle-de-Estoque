<?php
$pageTitle = 'Relatorio de Divergencias';
$inventario = $inventario ?? [];
$itens = $itens ?? [];
$pageSubtitle = 'Comparativo entre quantidade do sistema e quantidade contada fisicamente.';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

$totalFalta = 0;
$totalSobra = 0;
$totalPendentes = 0;
$totalDivergentes = 0;

foreach ($itens as $item) {
    if ($item['quantidade_contada'] === null || $item['quantidade_contada'] === '') {
        $totalPendentes++;
        continue;
    }

    $diferenca = (int) ($item['diferenca'] ?? 0);
    if ($diferenca > 0) {
        $totalSobra += $diferenca;
        $totalDivergentes++;
    } elseif ($diferenca < 0) {
        $totalFalta += abs($diferenca);
        $totalDivergentes++;
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <h2>Divergencias - <?= esc($inventario['titulo'] ?? '') ?></h2>
                <p>Revise as diferencas antes de aprovar qualquer ajuste no estoque oficial.</p>
            </div>
            <div class="dashboard-actions">
                <a href="index.php?acao=inventario_detalhar&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    Voltar aos detalhes
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-4 mb-3">
        <article class="metric-card">
            <p class="metric-label">Itens</p>
            <strong class="metric-value"><?= count($itens) ?></strong>
        </article>
        <article class="metric-card">
            <p class="metric-label">Divergentes</p>
            <strong class="metric-value"><?= $totalDivergentes ?></strong>
        </article>
        <article class="metric-card">
            <p class="metric-label">Sobras</p>
            <strong class="metric-value text-success">+<?= $totalSobra ?></strong>
        </article>
        <article class="metric-card">
            <p class="metric-label">Faltas</p>
            <strong class="metric-value text-danger">-<?= $totalFalta ?></strong>
        </article>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Itens do inventario</h2>
        </div>

        <?php if (empty($itens)): ?>
            <div class="empty-state">Nenhum item encontrado.</div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Codigo</th>
                            <th>Qtd. Sistema</th>
                            <th>Qtd. Contada</th>
                            <th>Divergencia</th>
                            <th>Situacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <?php
                                $qtdSistema = (int) ($item['quantidade_sistema'] ?? 0);
                                $qtdContada = $item['quantidade_contada'] !== null ? (int) $item['quantidade_contada'] : null;
                                $diferenca = (int) ($item['diferenca'] ?? 0);
                                $situacao = 'Conferido';
                                $badge = 'badge-info';

                                if ($qtdContada === null) {
                                    $situacao = 'Pendente';
                                    $badge = 'badge-muted';
                                } elseif ($diferenca > 0) {
                                    $situacao = 'Sobra';
                                    $badge = 'badge-success';
                                } elseif ($diferenca < 0) {
                                    $situacao = 'Falta';
                                    $badge = 'badge-danger';
                                }
                            ?>
                            <tr>
                                <td><strong><?= esc($item['produto_nome'] ?? '') ?></strong></td>
                                <td><?= esc($item['produto_codigo'] ?? '-') ?></td>
                                <td><?= $qtdSistema ?></td>
                                <td><?= $qtdContada === null ? '<span class="text-muted">Nao contada</span>' : $qtdContada ?></td>
                                <td><?= $qtdContada === null ? '-' : ($diferenca > 0 ? '+' . $diferenca : $diferenca) ?></td>
                                <td><span class="badge <?= esc($badge) ?>"><?= esc($situacao) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if (($inventario['status'] ?? '') === 'em_conferencia' && Auth::isAdmin()): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3>Aprovar Ajustes de Estoque</h3>
                <?php if ($totalPendentes > 0): ?>
                    <p>Existem <?= $totalPendentes ?> itens sem contagem. Complete a contagem antes de aprovar.</p>
                <?php else: ?>
                    <p>A aprovacao atualiza o estoque oficial e registra a auditoria.</p>
                <?php endif; ?>
            </div>
            <div class="p-3">
                <form action="index.php?acao=inventario_aprovar" method="POST" data-confirm="Tem certeza que deseja aprovar este inventário e atualizar o estoque?" data-confirm-ok="Aprovar">
                    <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">
                    <input type="hidden" name="inventario_id" value="<?= (int) ($inventario['id'] ?? 0) ?>">
                    <button
                        type="submit"
                        class="btn btn-success btn-lg"
                        <?= $totalPendentes > 0 ? 'disabled' : '' ?>
                    >
                        Aprovar Ajustes e Finalizar Inventario
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
