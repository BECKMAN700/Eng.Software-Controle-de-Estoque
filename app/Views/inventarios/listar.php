<?php
require_once __DIR__ . '/../partials/icons.php';
$pageTitle = 'Inventários';
$pageSubtitle = 'Acompanhe as contagens, aberturas, conferências e auditorias de estoque.';
$inventarios = $inventarios ?? [];

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
            return '<span class="badge badge-warning">Em conferência</span>';
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
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Inventários cadastrados</h2>
                <p>Lista completa de todos os fluxos de inventário iniciados no sistema.</p>
            </div>

            <a href="index.php?acao=inventario_criar" class="btn btn-primary">
                Abrir novo inventário
            </a>
        </div>

        <?php if (empty($inventarios)): ?>
            <?= uiEmptyState('inventory', 'Nenhum inventário ainda', 'Abra um inventário para iniciar a contagem física do estoque.', 'Abrir novo inventário', 'index.php?acao=inventario_criar') ?>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Inventário</th>
                            <th>Filtro/Categoria</th>
                            <th>Status</th>
                            <th>Responsável</th>
                            <th>Total Itens</th>
                            <th>Divergências</th>
                            <th>Aberto em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventarios as $inv): ?>
                            <?php
                                $idInv = (int) ($inv['id'] ?? 0);
                                $totalItens = (int) ($inv['total_itens'] ?? 0);
                                $totalDivergencias = (int) ($inv['total_divergencias'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($inv['titulo'] ?? '') ?></div>
                                    <div class="product-code">ID #<?= $idInv ?></div>
                                </td>
                                <td><?= esc($inv['categoria'] ?? 'Todos os produtos') ?></td>
                                <td><?= formatarStatusInventario($inv['status'] ?? '') ?></td>
                                <td><?= esc($inv['criado_por_nome'] ?? 'Desconhecido') ?></td>
                                <td><?= $totalItens ?></td>
                                <td>
                                    <?php if ($totalDivergencias > 0): ?>
                                        <span class="badge badge-danger"><?= $totalDivergencias ?> divergências</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Conferido</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($inv['criado_em'] ?? 'now')) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="index.php?acao=inventario_detalhar&id=<?= $idInv ?>" class="btn btn-secondary btn-sm">
                                            Detalhar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
