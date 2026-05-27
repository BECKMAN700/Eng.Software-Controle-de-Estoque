<?php
$pageTitle = 'Contagem Fisica';
$inventario = $inventario ?? [];
$itens = $itens ?? [];
$pageSubtitle = 'Registre a quantidade fisica contada de cada produto.';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarStatusInventario')) {
    function formatarStatusInventario($status): string
    {
        $status = strtolower((string) $status);

        if ($status === 'aberto') {
            return '<span class="badge badge-success">Aberto</span>';
        }

        if ($status === 'em_conferencia') {
            return '<span class="badge badge-warning">Em conferencia</span>';
        }

        if ($status === 'aprovado') {
            return '<span class="badge badge-info">Aprovado</span>';
        }

        if ($status === 'cancelado') {
            return '<span class="badge badge-danger">Cancelado</span>';
        }

        return '<span class="badge badge-muted">' . esc($status) . '</span>';
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <h2><?= esc($inventario['titulo'] ?? 'Contagem de Inventario') ?></h2>
                <p><?= esc($inventario['observacao'] ?? 'Registre as quantidades encontradas fisicamente.') ?></p>
            </div>
            <div class="dashboard-actions">
                <a href="index.php?acao=inventario_detalhar&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    Voltar aos detalhes
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
            <h2>Registrar Contagem Fisica</h2>
            <p>Preencha a quantidade real encontrada para cada produto.</p>
        </div>

        <form action="index.php?acao=inventario_salvar_contagem" method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">
            <input type="hidden" name="inventario_id" value="<?= (int) ($inventario['id'] ?? 0) ?>">

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Codigo</th>
                            <th>Categoria</th>
                            <th>Unidade</th>
                            <th>Qtd. Sistema</th>
                            <th>Qtd. Contada</th>
                            <th>Observacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($item['produto_nome'] ?? '') ?></strong>
                                    <small class="text-muted">ID: <?= (int) ($item['produto_id'] ?? 0) ?></small>
                                </td>
                                <td><?= esc($item['produto_codigo'] ?? '-') ?></td>
                                <td><?= esc($item['produto_categoria'] ?? '-') ?></td>
                                <td><?= esc($item['produto_unidade'] ?? '-') ?></td>
                                <td><span class="stock-pill situacao-ok"><?= (int) ($item['quantidade_sistema'] ?? 0) ?></span></td>
                                <td>
                                    <input
                                        type="number"
                                        name="contagens[<?= (int) $item['id'] ?>]"
                                        class="form-control input-contagem"
                                        min="0"
                                        value="<?= esc($item['quantidade_contada'] ?? '') ?>"
                                        placeholder="0"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="observacoes[<?= (int) $item['id'] ?>]"
                                        class="form-control"
                                        value="<?= esc($item['observacao'] ?? '') ?>"
                                        placeholder="Observacao..."
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions mt-3">
                <button type="submit" class="btn btn-primary btn-lg">Salvar Contagens</button>
                <a href="index.php?acao=inventario_detalhar&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
