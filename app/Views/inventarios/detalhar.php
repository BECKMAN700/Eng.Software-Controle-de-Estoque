<?php
$pageTitle = 'Detalhes do Inventario';
$inventario = $inventario ?? [];
$itens = $inventario['itens'] ?? [];
$pageSubtitle = 'Visualize as informacoes do inventario e os itens gravados na abertura.';

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
                <h2><?= esc($inventario['titulo'] ?? 'Inventario') ?></h2>
                <p>Informacoes consolidadas sobre a abertura do inventario.</p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=inventarios" class="btn btn-secondary">Voltar para lista</a>

                <?php if (in_array($inventario['status'] ?? '', ['aberto', 'em_conferencia'], true)): ?>
                    <a href="index.php?acao=inventario_contagem&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-primary">
                        Iniciar / Continuar Contagem
                    </a>
                <?php endif; ?>

                <?php if (($inventario['status'] ?? '') === 'em_conferencia'): ?>
                    <a href="index.php?acao=inventario_divergencias&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-warning">
                        Ver Divergencias
                    </a>
                <?php endif; ?>

                <?php if (Auth::isAdmin() && ($inventario['status'] ?? '') === 'em_conferencia'): ?>
                    <a href="index.php?acao=inventario_divergencias&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-success">
                        Aprovar Ajustes
                    </a>
                <?php endif; ?>

                <?php if (($inventario['status'] ?? '') === 'aprovado'): ?>
                    <a href="index.php?acao=inventario_auditoria&id=<?= (int) ($inventario['id'] ?? 0) ?>" class="btn btn-secondary">
                        Ver Auditoria
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-4 mt-2">
            <article class="metric-card">
                <p class="metric-label">Status atual</p>
                <div class="mt-1"><?= formatarStatusInventario($inventario['status'] ?? '') ?></div>
                <p class="metric-description">Estado do fluxo de conferencia.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Responsavel pela abertura</p>
                <strong class="metric-value" style="font-size: 1.2rem;"><?= esc($inventario['criado_por_nome'] ?? 'Desconhecido') ?></strong>
                <p class="metric-description">Usuario que realizou a abertura do processo.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Data de abertura</p>
                <strong class="metric-value" style="font-size: 1.2rem;"><?= date('d/m/Y H:i', strtotime($inventario['criado_em'] ?? 'now')) ?></strong>
                <p class="metric-description">Data e hora do registro inicial.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Filtro de categoria</p>
                <strong class="metric-value" style="font-size: 1.2rem;"><?= esc($inventario['categoria'] ?? 'Todos os produtos') ?></strong>
                <p class="metric-description">Restricao aplicada ao inventario.</p>
            </article>
        </div>

        <?php if (!empty($inventario['observacao'])): ?>
            <div class="form-group mt-2">
                <label><strong>Observacoes / Notas do Inventario:</strong></label>
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
                <h2>Itens do Inventario</h2>
                <p>Produtos incluidos com a quantidade gravada no momento da abertura.</p>
            </div>
        </div>

        <?php if (empty($itens)): ?>
            <div class="empty-state">
                Nenhum produto associado a este inventario.
            </div>
        <?php else: ?>
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
                            <th>Divergencia</th>
                            <th>Observacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <?php
                                $diferenca = $item['diferenca'];
                                $qtdContada = $item['quantidade_contada'];
                                $qtdSistema = (int) $item['quantidade_sistema'];

                                $badgeClasse = 'badge-muted';
                                $badgeTexto = 'Nao informada';
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
                                        $diffTexto = '0 (Sem divergencia)';
                                    }
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= esc($item['produto_nome'] ?? '') ?></div>
                                    <div class="product-code">ID #<?= (int) ($item['produto_id'] ?? 0) ?></div>
                                </td>
                                <td><?= esc($item['produto_codigo'] ?? 'Sem codigo') ?></td>
                                <td><?= esc($item['produto_categoria'] ?? 'Sem categoria') ?></td>
                                <td><?= esc($item['produto_unidade'] ?? '-') ?></td>
                                <td><span class="stock-pill situacao-ok"><?= $qtdSistema ?></span></td>
                                <td><span class="badge <?= esc($badgeClasse) ?>"><?= esc($badgeTexto) ?></span></td>
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
