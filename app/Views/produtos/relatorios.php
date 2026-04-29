<?php
$pageTitle = 'Relatórios';
$pageSubtitle = 'Resumo geral dos produtos, valores em estoque e movimentações recentes.';

$produtos = $produtos ?? [];
$produtosAbaixoDoMinimo = $produtosAbaixoDoMinimo ?? [];
$produtosNoMinimo = $produtosNoMinimo ?? [];
$produtosAcimaDoMaximo = $produtosAcimaDoMaximo ?? [];
$ultimasMovimentacoes = $ultimasMovimentacoes ?? [];

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarDinheiro')) {
    function formatarDinheiro($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

if (!function_exists('formatarDataHora')) {
    function formatarDataHora($dataHora): string
    {
        if (empty($dataHora)) {
            return '-';
        }

        $timestamp = strtotime($dataHora);

        if (!$timestamp) {
            return esc($dataHora);
        }

        return date('d/m/Y H:i', $timestamp);
    }
}

if (!function_exists('formatarMotivoRelatorio')) {
    function formatarMotivoRelatorio($motivo): string
    {
        $motivos = [
            'compra' => 'Compra',
            'devolucao' => 'Devolução',
            'transferencia' => 'Transferência',
            'venda' => 'Venda',
            'consumo_interno' => 'Consumo interno',
            'perda' => 'Perda',
            'avaria' => 'Avaria',
            'entrada_manual' => 'Entrada manual',
            'saida_manual' => 'Saída manual',
        ];

        $motivo = (string) $motivo;

        return $motivos[$motivo] ?? ucfirst(str_replace('_', ' ', $motivo));
    }
}

$totalProdutos = count($produtos);
$totalUnidades = 0;
$valorTotalEstoque = 0;
$totalAtivos = 0;
$totalInativos = 0;
$totalDescontinuados = 0;

foreach ($produtos as $produto) {
    $quantidade = (int) ($produto['quantidade'] ?? 0);
    $preco = (float) ($produto['preco'] ?? 0);
    $status = strtolower((string) ($produto['status'] ?? ''));

    $totalUnidades += $quantidade;
    $valorTotalEstoque += $quantidade * $preco;

    if ($status === 'ativo') {
        $totalAtivos++;
    } elseif ($status === 'inativo') {
        $totalInativos++;
    } elseif ($status === 'descontinuado') {
        $totalDescontinuados++;
    }
}

$produtosMaiorQuantidade = $produtos;

usort($produtosMaiorQuantidade, function ($a, $b) {
    return (int) ($b['quantidade'] ?? 0) <=> (int) ($a['quantidade'] ?? 0);
});

$produtosMaiorQuantidade = array_slice($produtosMaiorQuantidade, 0, 5);

ob_start();
?>

<section class="page-section">
    <div class="grid grid-4">
        <article class="metric-card">
            <p class="metric-label">Produtos cadastrados</p>
            <strong class="metric-value"><?= $totalProdutos ?></strong>
            <p class="metric-description">Total de produtos registrados no sistema.</p>
        </article>

        <article class="metric-card summary-card-info">
            <p class="metric-label">Unidades em estoque</p>
            <strong class="metric-value"><?= $totalUnidades ?></strong>
            <p class="metric-description">Soma da quantidade atual dos produtos.</p>
        </article>

        <article class="metric-card summary-card-success">
            <p class="metric-label">Valor estimado</p>
            <strong class="metric-value metric-money"><?= formatarDinheiro($valorTotalEstoque) ?></strong>
            <p class="metric-description">Quantidade multiplicada pelo preço cadastrado.</p>
        </article>

        <article class="metric-card summary-card-danger">
            <p class="metric-label">Abaixo do mínimo</p>
            <strong class="metric-value"><?= count($produtosAbaixoDoMinimo) ?></strong>
            <p class="metric-description">Produtos que precisam de atenção.</p>
        </article>
    </div>
</section>

<section class="page-section">
    <div class="grid grid-3">
        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Status dos produtos</h2>
                    <p>Distribuição por situação cadastral.</p>
                </div>
            </div>

            <div class="report-list">
                <div class="report-list-item">
                    <span>Ativos</span>
                    <strong><?= $totalAtivos ?></strong>
                </div>

                <div class="report-list-item">
                    <span>Inativos</span>
                    <strong><?= $totalInativos ?></strong>
                </div>

                <div class="report-list-item">
                    <span>Descontinuados</span>
                    <strong><?= $totalDescontinuados ?></strong>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Alertas de estoque</h2>
                    <p>Resumo dos limites mínimo e máximo.</p>
                </div>
            </div>

            <div class="report-list">
                <div class="report-list-item danger">
                    <span>Abaixo do mínimo</span>
                    <strong><?= count($produtosAbaixoDoMinimo) ?></strong>
                </div>

                <div class="report-list-item warning">
                    <span>No mínimo</span>
                    <strong><?= count($produtosNoMinimo) ?></strong>
                </div>

                <div class="report-list-item info">
                    <span>Acima do máximo</span>
                    <strong><?= count($produtosAcimaDoMaximo) ?></strong>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Ações rápidas</h2>
                    <p>Acesse as principais áreas do sistema.</p>
                </div>
            </div>

            <div class="quick-actions-list">
                <a href="index.php?acao=listar" class="btn btn-secondary">Painel</a>
                <a href="index.php?acao=catalogo" class="btn btn-secondary">Catálogo</a>
                <a href="index.php?acao=criar" class="btn btn-primary">Novo produto</a>
            </div>
        </article>
    </div>
</section>

<section class="page-section">
    <div class="grid grid-2">
        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Produtos com maior quantidade</h2>
                    <p>Os 5 produtos com mais unidades em estoque.</p>
                </div>
            </div>

            <?php if (empty($produtosMaiorQuantidade)): ?>
                <div class="empty-state">
                    Nenhum produto cadastrado.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table table-compact">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($produtosMaiorQuantidade as $produto): ?>
                                <?php
                                    $quantidade = (int) ($produto['quantidade'] ?? 0);
                                    $preco = (float) ($produto['preco'] ?? 0);
                                ?>

                                <tr>
                                    <td>
                                        <div class="product-name"><?= esc($produto['nome'] ?? '') ?></div>
                                        <div class="product-code"><?= esc($produto['codigo'] ?? 'Sem código') ?></div>
                                    </td>

                                    <td>
                                        <span class="stock-pill"><?= $quantidade ?></span>
                                    </td>

                                    <td>
                                        <?= formatarDinheiro($quantidade * $preco) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>

        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Últimas movimentações</h2>
                    <p>Entradas e saídas registradas recentemente.</p>
                </div>
            </div>

            <?php if (empty($ultimasMovimentacoes)): ?>
                <div class="empty-state">
                    Nenhuma movimentação registrada ainda.
                </div>
            <?php else: ?>
                <div class="movement-list">
                    <?php foreach ($ultimasMovimentacoes as $movimentacao): ?>
                        <?php
                            $tipo = strtolower((string) ($movimentacao['tipo'] ?? ''));
                            $quantidade = (int) ($movimentacao['quantidade'] ?? 0);
                            $badgeClasse = $tipo === 'entrada' ? 'badge-success' : 'badge-danger';
                            $sinal = $tipo === 'entrada' ? '+' : '-';
                        ?>

                        <div class="movement-item">
                            <div>
                                <strong><?= esc($movimentacao['produto_nome'] ?? 'Produto') ?></strong>
                                <span>
                                    <?= esc(formatarMotivoRelatorio($movimentacao['motivo'] ?? '')) ?>
                                    —
                                    <?= formatarDataHora($movimentacao['data_hora'] ?? '') ?>
                                </span>
                            </div>

                            <span class="badge <?= $badgeClasse ?>">
                                <?= $sinal . $quantidade ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Produtos em alerta</h2>
                <p>Produtos que merecem acompanhamento por limite mínimo ou máximo.</p>
            </div>

            <a href="index.php?acao=listar#alertas-estoque" class="btn btn-secondary">
                Ver alertas no painel
            </a>
        </div>

        <?php if (empty($produtosAbaixoDoMinimo) && empty($produtosNoMinimo) && empty($produtosAcimaDoMaximo)): ?>
            <div class="empty-state">
                Nenhum produto em alerta no momento.
            </div>
        <?php else: ?>
            <div class="alert-list">
                <?php foreach ($produtosAbaixoDoMinimo as $produto): ?>
                    <div class="alert-item">
                        <div>
                            <strong><?= esc($produto['nome'] ?? '') ?></strong>
                            <span>
                                Abaixo do mínimo —
                                Atual: <?= (int) ($produto['quantidade'] ?? 0) ?> /
                                Mínimo: <?= (int) ($produto['estoque_minimo'] ?? 0) ?>
                            </span>
                        </div>

                        <a class="btn btn-primary btn-sm" href="index.php?acao=entrada&id=<?= (int) ($produto['id'] ?? 0) ?>">
                            Repor
                        </a>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($produtosNoMinimo as $produto): ?>
                    <div class="alert-item">
                        <div>
                            <strong><?= esc($produto['nome'] ?? '') ?></strong>
                            <span>
                                No estoque mínimo —
                                Atual: <?= (int) ($produto['quantidade'] ?? 0) ?> /
                                Mínimo: <?= (int) ($produto['estoque_minimo'] ?? 0) ?>
                            </span>
                        </div>

                        <a class="btn btn-secondary btn-sm" href="index.php?acao=entrada&id=<?= (int) ($produto['id'] ?? 0) ?>">
                            Registrar entrada
                        </a>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($produtosAcimaDoMaximo as $produto): ?>
                    <div class="alert-item">
                        <div>
                            <strong><?= esc($produto['nome'] ?? '') ?></strong>
                            <span>
                                Acima do máximo —
                                Atual: <?= (int) ($produto['quantidade'] ?? 0) ?> /
                                Máximo: <?= (int) ($produto['estoque_maximo'] ?? 0) ?>
                            </span>
                        </div>

                        <a class="btn btn-secondary btn-sm" href="index.php?acao=historico_movimentacoes&id=<?= (int) ($produto['id'] ?? 0) ?>">
                            Ver histórico
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';