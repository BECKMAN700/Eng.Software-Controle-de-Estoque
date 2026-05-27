<?php
$pageTitle = 'Relatorio de limites de estoque';
$pageSubtitle = 'Produtos abaixo do estoque minimo ou acima do estoque maximo.';
$divergencias = $divergencias ?? [];

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
                <h2>Produtos fora dos limites</h2>
                <p>Acompanhe produtos que precisam de reabastecimento ou ajuste de estoque maximo.</p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=listar" class="btn btn-secondary">Voltar ao painel</a>
            </div>
        </div>

        <?php if (empty($divergencias)): ?>
            <div class="empty-state">
                Nenhum produto fora dos limites de estoque.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Minimo</th>
                            <th>Maximo</th>
                            <th>Situacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($divergencias as $produto): ?>
                            <?php
                                $quantidade = (int) ($produto['quantidade'] ?? 0);
                                $minimo = (int) ($produto['estoque_minimo'] ?? 0);
                                $maximo = $produto['estoque_maximo'];
                                $situacao = 'Normal';
                                $badge = 'badge-info';

                                if ($quantidade < $minimo) {
                                    $situacao = 'Abaixo do minimo';
                                    $badge = 'badge-danger';
                                }

                                if ($maximo !== null && $maximo !== '' && $quantidade > (int) $maximo) {
                                    $situacao = 'Acima do maximo';
                                    $badge = 'badge-warning';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                    <div class="product-code"><?= esc($produto['codigo'] ?? 'Sem codigo') ?></div>
                                </td>
                                <td><?= esc($produto['categoria'] ?? '-') ?></td>
                                <td><?= $quantidade ?></td>
                                <td><?= $minimo ?></td>
                                <td><?= $maximo === null || $maximo === '' ? '-' : (int) $maximo ?></td>
                                <td><span class="badge <?= esc($badge) ?>"><?= esc($situacao) ?></span></td>
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
