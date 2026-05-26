<?php
$pageTitle = 'Contagem Física';
$inventario = $inventario ?? [];
$itens = $itens ?? [];
$pageSubtitle = 'Registre a quantidade física contada de cada produto.';

if (!function_exists('esc')) {
    function esc($valor): string
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
                <h2><?= esc($inventario['titulo'] ?? 'Contagem de Inventário') ?></h2>
                <p><?= esc($inventario['observacao'] ?? 'Registre as quantidades encontradas fisicamente.') ?></p>
            </div>
            <div class="dashboard-actions">
                <a href="index.php?acao=inventario_detalhar&id=<?= (int)($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    ← Voltar aos Detalhes
                </a>
            </div>
        </div>

        <div class="grid grid-4 mt-2">
            <article class="metric-card">
                <p class="metric-label">Status</p>
                <div><?= formatarStatusInventario($inventario['status'] ?? '') ?></div>
            </article>
            <article class="metric-card">
                <p class="metric-label">Total de Itens</p>
                <strong class="metric-value"><?= count($itens) ?></strong>
            </article>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Registrar Contagem Física</h2>
            <p>Preencha a quantidade real encontrada para cada produto.</p>
        </div>

        <form action="index.php?acao=inventario_salvar_contagem" method="POST">
            <input type="hidden" name="inventario_id" value="<?= (int)($inventario['id'] ?? 0) ?>">

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th>Categoria</th>
                            <th>Unidade</th>
                            <th>Qtd. Sistema</th>
                            <th>Qtd. Contada (Física)</th>
                            <th>Observação (opcional)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($item['produto_nome'] ?? '') ?></strong>
                                    <small class="text-muted">ID: <?= (int)($item['produto_id'] ?? 0) ?></small>
                                </td>
                                <td><?= esc($item['produto_codigo'] ?? '-') ?></td>
                                <td><?= esc($item['produto_categoria'] ?? '-') ?></td>
                                <td><?= esc($item['produto_unidade'] ?? '-') ?></td>
                                <td>
                                    <span class="stock-pill situacao-ok">
                                        <?= (int)($item['quantidade_sistema'] ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <input 
                                        type="number" 
                                        name="contagens[<?= (int)$item['id'] ?>]" 
                                        class="form-control input-contagem"
                                        min="0"
                                        value="<?= esc($item['quantidade_contada'] ?? '') ?>"
                                        placeholder="0"
                                    >
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        name="observacao[<?= (int)$item['id'] ?>]" 
                                        class="form-control"
                                        value="<?= esc($item['observacao'] ?? '') ?>"
                                        placeholder="Observação..."
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    💾 Salvar Contagens
                </button>
                <a href="index.php?acao=inventario_detalhar&id=<?= (int)($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';