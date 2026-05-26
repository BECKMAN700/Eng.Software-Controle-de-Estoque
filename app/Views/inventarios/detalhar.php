<?php
$pageTitle = 'Detalhes do Inventário';
$inventario = $inventario ?? [];
$itens = $inventario['itens'] ?? [];
$pageSubtitle = 'Visualize as informações do inventário e a listagem de itens snapshot na abertura.';

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
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <h2><?= esc($inventario['titulo'] ?? 'Inventário') ?></h2>
                <p>Informações consolidadas sobre a abertura do inventário.</p>
            </div>

                <div class="dashboard-actions">
        <a href="index.php?acao=inventarios" class="btn btn-secondary">
            ← Voltar para lista
        </a>
        
        <?php if (in_array($inventario['status'] ?? '', ['aberto', 'em_conferencia'])): ?>
            <a href="index.php?acao=inventario_contagem&id=<?= (int)($inventario['id'] ?? 0) ?>" 
               class="btn btn-primary">
                📋 Iniciar / Continuar Contagem
            </a>
        <?php endif; ?>

        <?php if (($inventario['status'] ?? '') === 'em_conferencia'): ?>
            <a href="index.php?acao=inventario_divergencias&id=<?= (int)($inventario['id'] ?? 0) ?>" 
               class="btn btn-warning">
                📊 Ver Divergências
            </a>
        <?php endif; ?>

        <?php if (Auth::isAdmin() && ($inventario['status'] ?? '') === 'em_conferencia'): ?>
            <a href="index.php?acao=inventario_divergencias&id=<?= (int)($inventario['id'] ?? 0) ?>" 
               class="btn btn-success">
                ✅ Aprovar Ajustes
            </a>
        <?php endif; ?>
    </div>

        <div class="grid grid-4 mt-2">
            <article class="metric-card">
                <p class="metric-label">Status atual</p>
                <div class="mt-1"><?= formatarStatusInventario($inventario['status'] ?? '') ?></div>
                <p class="metric-description">Estado do fluxo de conferência.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Responsável pela abertura</p>
                <strong class="metric-value" style="font-size: 1.2rem;"><?= esc($inventario['criado_por_nome'] ?? 'Desconhecido') ?></strong>
                <p class="metric-description">Usuário que realizou a abertura do processo.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Data de abertura</p>
                <strong class="metric-value" style="font-size: 1.2rem;"><?= date('d/m/Y H:i', strtotime($inventario['criado_em'] ?? 'now')) ?></strong>
                <p class="metric-description">Data e hora do registro inicial.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Filtro de categoria</p>
                <strong class="metric-value" style="font-size: 1.2rem;"><?= esc($inventario['categoria'] ?? 'Todos os produtos') ?></strong>
                <p class="metric-description">Restrição aplicada ao inventário.</p>
            </article>
        </div>

        <?php if (!empty($inventario['observacao'])): ?>
            <div class="form-group mt-2">
                <label><strong>Observações / Notas do Inventário:</strong></label>
                <p class="form-help" style="background-color: var(--card-bg-subtle, #f8f9fa); padding: 10px; border-radius: 4px; border-left: 4px solid var(--primary); margin-top: 5px;">
                    <?= nl2br(esc($inventario['observacao'])) ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Itens do Inventário</h2>
                <p>Lista dos produtos incluídos com a quantidade gravada do sistema no momento da abertura.</p>
            </div>
        </div>

        <?php if (empty($itens)): ?>
            <div class="empty-state">
                Nenhum produto associado a este inventário.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th>Categoria</th>
                            <th>Unidade</th>
                            <th>Qtd. Sistema (Abertura)</th>
                            <th>Qtd. Contada</th>
                            <th>Divergência</th>
                            <th>Observação do item</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <?php
                                $diferenca = $item['diferenca'];
                                $qtdContada = $item['quantidade_contada'];
                                $qtdSistema = (int) $item['quantidade_sistema'];
                                
                                $badgeClasse = 'badge-muted';
                                $badgeTexto = 'Não informada';
                                $diffTexto = '-';

                                if ($qtdContada !== null && $qtdContada !== '') {
                                    $qtdContada = (int) $qtdContada;
                                    $badgeTexto = $qtdContada . ' un.';
                                    if ($diferenca > 0) {
                                        $badgeClasse = 'badge-success';
                                        $diffTexto = '+' . $diferenca . ' (Sobra)';
                                    } elseif ($diferenca < 0) {
                                        $badgeClasse = 'badge-danger';
                                        $diffTexto = $diferenca . ' (Falta)';
                                    } else {
                                        $badgeClasse = 'badge-info';
                                        $diffTexto = '0 (Sem divergência)';
                                    }
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($item['produto_name'] ?? '') ?></div>
                                    <div class="product-code">ID #<?= (int) ($item['produto_id'] ?? 0) ?></div>
                                </td>
                                <td><?= esc($item['produto_codigo'] ?? 'Sem código') ?></td>
                                <td><?= esc($item['produto_categoria'] ?? 'Sem categoria') ?></td>
                                <td><?= esc($item['produto_unidade'] ?? '-') ?></td>
                                <td>
                                    <span class="stock-pill situacao-ok">
                                        <?= $qtdSistema ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= esc($badgeClasse) ?>">
                                        <?= $badgeTexto ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($qtdContada !== null && $qtdContada !== ''): ?>
                                        <span class="badge <?= $diferenca === 0 ? 'badge-success' : 'badge-danger' ?>">
                                            <?= esc($diffTexto) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">Aguardando contagem</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item['observacao'] ?? '-') ?></td>
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
