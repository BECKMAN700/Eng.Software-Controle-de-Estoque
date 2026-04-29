<?php
$pageTitle = 'Histórico de movimentações';
$pageSubtitle = 'Consulte todas as entradas e saídas registradas para este produto.';

$produto = $produto ?? [];
$historico = $historico ?? [];

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
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

if (!function_exists('formatarTipoMovimentacao')) {
    function formatarTipoMovimentacao($tipo): string
    {
        $tipo = strtolower((string) $tipo);

        if ($tipo === 'entrada') {
            return '<span class="badge badge-success">Entrada</span>';
        }

        if ($tipo === 'saida') {
            return '<span class="badge badge-danger">Saída</span>';
        }

        return '<span class="badge badge-muted">' . esc($tipo ?: 'Não informado') . '</span>';
    }
}

if (!function_exists('formatarMotivo')) {
    function formatarMotivo($motivo): string
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

$totalEntradas = 0;
$totalSaidas = 0;
$quantidadeEntrada = 0;
$quantidadeSaida = 0;

foreach ($historico as $movimentacao) {
    $tipo = strtolower((string) ($movimentacao['tipo'] ?? ''));
    $quantidade = (int) ($movimentacao['quantidade'] ?? 0);

    if ($tipo === 'entrada') {
        $totalEntradas++;
        $quantidadeEntrada += $quantidade;
    }

    if ($tipo === 'saida') {
        $totalSaidas++;
        $quantidadeSaida += $quantidade;
    }
}

$saldoMovimentado = $quantidadeEntrada - $quantidadeSaida;

ob_start();
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2><?= esc($produto['nome'] ?? 'Produto não informado') ?></h2>
                <p>
                    Código:
                    <strong><?= esc($produto['codigo'] ?? 'Sem código') ?></strong>
                    —
                    Categoria:
                    <strong><?= esc($produto['categoria'] ?? 'Sem categoria') ?></strong>
                </p>
            </div>

            <div class="dashboard-actions">
                <a href="index.php?acao=entrada&id=<?= (int) ($produto['id'] ?? 0) ?>" class="btn btn-primary">
                    Registrar entrada
                </a>

                <a href="index.php?acao=saida&id=<?= (int) ($produto['id'] ?? 0) ?>" class="btn btn-secondary">
                    Registrar saída
                </a>

                <a href="index.php?acao=listar" class="btn btn-secondary">
                    Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-4">
            <article class="metric-card">
                <p class="metric-label">Quantidade atual</p>
                <strong class="metric-value"><?= (int) ($produto['quantidade'] ?? 0) ?></strong>
                <p class="metric-description">
                    Unidade: <?= esc($produto['unidade'] ?? '-') ?>
                </p>
            </article>

            <article class="metric-card summary-card-info">
                <p class="metric-label">Total de movimentações</p>
                <strong class="metric-value"><?= count($historico) ?></strong>
                <p class="metric-description">
                    Entradas e saídas registradas.
                </p>
            </article>

            <article class="metric-card summary-card-success">
                <p class="metric-label">Entradas</p>
                <strong class="metric-value"><?= $quantidadeEntrada ?></strong>
                <p class="metric-description">
                    <?= $totalEntradas ?> movimentação(ões) de entrada.
                </p>
            </article>

            <article class="metric-card summary-card-danger">
                <p class="metric-label">Saídas</p>
                <strong class="metric-value"><?= $quantidadeSaida ?></strong>
                <p class="metric-description">
                    <?= $totalSaidas ?> movimentação(ões) de saída.
                </p>
            </article>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Resumo do histórico</h2>
                <p>Veja o saldo total movimentado para este produto.</p>
            </div>
        </div>

        <div class="grid grid-3">
            <article class="metric-card">
                <p class="metric-label">Total recebido</p>
                <strong class="metric-value"><?= $quantidadeEntrada ?></strong>
                <p class="metric-description">Soma de todas as entradas registradas.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Total retirado</p>
                <strong class="metric-value"><?= $quantidadeSaida ?></strong>
                <p class="metric-description">Soma de todas as saídas registradas.</p>
            </article>

            <article class="metric-card <?= $saldoMovimentado >= 0 ? 'summary-card-info' : 'summary-card-warning' ?>">
                <p class="metric-label">Saldo movimentado</p>
                <strong class="metric-value"><?= $saldoMovimentado ?></strong>
                <p class="metric-description">Entradas menos saídas.</p>
            </article>
        </div>
    </div>
</section>

<section class="page-section" id="movimentacoes">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Movimentações registradas</h2>
                <p>Histórico detalhado de entradas e saídas deste produto.</p>
            </div>
        </div>

        <?php if (empty($historico)): ?>
            <div class="empty-state">
                Nenhuma movimentação registrada para este produto.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th>Quantidade</th>
                            <th>Observação</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($historico as $movimentacao): ?>
                            <?php
                                $tipo = strtolower((string) ($movimentacao['tipo'] ?? ''));
                                $quantidade = (int) ($movimentacao['quantidade'] ?? 0);
                            ?>

                            <tr>
                                <td>
                                    <strong><?= formatarDataHora($movimentacao['data_hora'] ?? '') ?></strong>
                                </td>

                                <td>
                                    <?= formatarTipoMovimentacao($tipo) ?>
                                </td>

                                <td>
                                    <?= esc(formatarMotivo($movimentacao['motivo'] ?? '')) ?>
                                </td>

                                <td>
                                    <?php if ($tipo === 'entrada'): ?>
                                        <span class="stock-pill situacao-ok">+<?= $quantidade ?></span>
                                    <?php elseif ($tipo === 'saida'): ?>
                                        <span class="stock-pill situacao-critico">-<?= $quantidade ?></span>
                                    <?php else: ?>
                                        <span class="stock-pill"><?= $quantidade ?></span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= !empty($movimentacao['observacao']) ? esc($movimentacao['observacao']) : '<span class="text-muted">Sem observação</span>' ?>
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