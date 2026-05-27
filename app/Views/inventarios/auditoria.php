<?php
$pageTitle = 'Auditoria do Inventario';
$inventario = $inventario ?? [];
$auditorias = $auditorias ?? [];
$pageSubtitle = 'Historico dos ajustes aprovados para este inventario.';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2><?= esc($inventario['titulo'] ?? 'Inventario') ?></h2>
                <p>Registros gerados na aprovacao dos ajustes de estoque.</p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=inventario_detalhar&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                    Voltar aos detalhes
                </a>
            </div>
        </div>

        <?php if (empty($auditorias)): ?>
            <div class="empty-state">
                Nenhum ajuste aprovado foi registrado para este inventario.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Usuario</th>
                            <th>Qtd. anterior</th>
                            <th>Qtd. nova</th>
                            <th>Diferenca</th>
                            <th>Motivo</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditorias as $auditoria): ?>
                            <?php $diferenca = (int) ($auditoria['diferenca'] ?? 0); ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($auditoria['produto_nome'] ?? '') ?></div>
                                    <div class="product-code"><?= esc($auditoria['produto_codigo'] ?? 'Sem codigo') ?></div>
                                </td>
                                <td><?= esc($auditoria['usuario_nome'] ?? 'Desconhecido') ?></td>
                                <td><?= (int) ($auditoria['quantidade_anterior'] ?? 0) ?></td>
                                <td><?= (int) ($auditoria['quantidade_nova'] ?? 0) ?></td>
                                <td>
                                    <span class="badge <?= $diferenca === 0 ? 'badge-info' : 'badge-danger' ?>">
                                        <?= $diferenca > 0 ? '+' . $diferenca : $diferenca ?>
                                    </span>
                                </td>
                                <td><?= esc($auditoria['motivo'] ?? '-') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($auditoria['criado_em'] ?? 'now')) ?></td>
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
