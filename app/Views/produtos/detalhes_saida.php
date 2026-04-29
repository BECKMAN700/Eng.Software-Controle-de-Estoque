<?php
$pageTitle = 'Detalhes das saídas';
$pageSubtitle = 'Consulte todas as saídas registradas para este produto.';

$produto = $produto ?? [];
$historicoSaidas = $historicoSaidas ?? [];

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarDataHoraSaida')) {
    function formatarDataHoraSaida($dataHora): string
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

if (!function_exists('formatarMotivoSaida')) {
    function formatarMotivoSaida($motivo): string
    {
        $motivos = [
            'venda' => 'Venda',
            'consumo_interno' => 'Consumo interno',
            'perda' => 'Perda',
            'avaria' => 'Avaria',
            'saida_manual' => 'Saída manual',
        ];

        $motivo = (string) $motivo;

        return $motivos[$motivo] ?? ucfirst(str_replace('_', ' ', $motivo));
    }
}

$totalSaidas = count($historicoSaidas);
$quantidadeTotalSaida = 0;

foreach ($historicoSaidas as $saida) {
    $quantidadeTotalSaida += (int) ($saida['quantidade'] ?? 0);
}

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
                <a href="index.php?acao=saida&id=<?= (int) ($produto['id'] ?? 0) ?>" class="btn btn-primary">
                    Registrar nova saída
                </a>

                <a href="index.php?acao=historico_movimentacoes&id=<?= (int) ($produto['id'] ?? 0) ?>" class="btn btn-secondary">
                    Histórico completo
                </a>

                <a href="index.php?acao=listar" class="btn btn-secondary">
                    Voltar ao painel
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

            <article class="metric-card summary-card-danger">
                <p class="metric-label">Saídas registradas</p>
                <strong class="metric-value"><?= $totalSaidas ?></strong>
                <p class="metric-description">
                    Total de movimentações de saída.
                </p>
            </article>

            <article class="metric-card summary-card-warning">
                <p class="metric-label">Total retirado</p>
                <strong class="metric-value"><?= $quantidadeTotalSaida ?></strong>
                <p class="metric-description">
                    Soma das quantidades retiradas.
                </p>
            </article>

            <article class="metric-card summary-card-info">
                <p class="metric-label">Status do produto</p>
                <strong class="product-card-title">
                    <?= esc(ucfirst($produto['status'] ?? 'Não informado')) ?>
                </strong>
                <p class="metric-description">
                    Situação cadastrada no estoque.
                </p>
            </article>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Informações do produto</h2>
                <p>Resumo cadastral do produto selecionado.</p>
            </div>
        </div>

        <div class="product-card-info-list">
            <div>
                <span>Nome</span>
                <strong><?= esc($produto['nome'] ?? 'Não informado') ?></strong>
            </div>

            <div>
                <span>Código</span>
                <strong><?= esc($produto['codigo'] ?? 'Sem código') ?></strong>
            </div>

            <div>
                <span>Categoria</span>
                <strong><?= esc($produto['categoria'] ?? 'Sem categoria') ?></strong>
            </div>

            <div>
                <span>Unidade</span>
                <strong><?= esc($produto['unidade'] ?? '-') ?></strong>
            </div>

            <div>
                <span>Estoque mínimo</span>
                <strong><?= (int) ($produto['estoque_minimo'] ?? 0) ?></strong>
            </div>

            <div>
                <span>Estoque máximo</span>
                <strong>
                    <?php if (($produto['estoque_maximo'] ?? '') !== '' && ($produto['estoque_maximo'] ?? null) !== null): ?>
                        <?= (int) $produto['estoque_maximo'] ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </strong>
            </div>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Histórico de saídas</h2>
                <p>Lista apenas das movimentações de saída deste produto.</p>
            </div>
        </div>

        <?php if (empty($historicoSaidas)): ?>
            <div class="empty-state">
                Este produto ainda não possui registros de saída.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Motivo</th>
                            <th>Quantidade</th>
                            <th>Observação</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($historicoSaidas as $saida): ?>
                            <tr>
                                <td>
                                    <strong><?= formatarDataHoraSaida($saida['data_hora'] ?? '') ?></strong>
                                </td>

                                <td>
                                    <span class="badge badge-danger">
                                        <?= esc(formatarMotivoSaida($saida['motivo'] ?? '')) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="stock-pill situacao-critico">
                                        -<?= (int) ($saida['quantidade'] ?? 0) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($saida['observacao'])): ?>
                                        <?= esc($saida['observacao']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sem observação</span>
                                    <?php endif; ?>
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