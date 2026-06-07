<?php

$pageTitle = 'Dashboard Gerencial';
$pageSubtitle = 'Indicadores estratégicos do estoque em tempo real.';

$resumo = $resumo ?? [];
$entradasSaidas = $entradasSaidas ?? [];

if (!function_exists('formatarDinheiro')) {
    function formatarDinheiro($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

ob_start();
?>

<section class="page-section">
    <div class="grid grid-4">

        <article class="metric-card">
            <p class="metric-label">Produtos cadastrados</p>
            <strong class="metric-value">
                <?= (int) ($resumo['total_produtos'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Total de produtos registrados.
            </p>
        </article>

        <article class="metric-card summary-card-info">
            <p class="metric-label">Unidades em estoque</p>
            <strong class="metric-value">
                <?= (int) ($resumo['total_unidades'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Soma das quantidades disponíveis.
            </p>
        </article>

        <article class="metric-card summary-card-success">
            <p class="metric-label">Valor do estoque</p>
            <strong class="metric-value metric-money">
                <?= formatarDinheiro($resumo['valor_total_estoque'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Valor estimado dos produtos.
            </p>
        </article>

        <article class="metric-card summary-card-danger">
            <p class="metric-label">Produtos críticos</p>
            <strong class="metric-value">
                <?= (int) ($resumo['produtos_criticos'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Abaixo do estoque mínimo.
            </p>
        </article>

    </div>
</section>

<section class="page-section">
    <div class="grid grid-2">

        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Movimentações (30 dias)</h2>
                    <p>Resumo das entradas e saídas recentes.</p>
                </div>
            </div>

            <div class="report-list">

                <div class="report-list-item">
                    <span>Total de entradas</span>
                    <strong>
                        <?= (int) ($entradasSaidas['entrada'] ?? 0) ?>
                    </strong>
                </div>

                <div class="report-list-item">
                    <span>Total de saídas</span>
                    <strong>
                        <?= (int) ($entradasSaidas['saida'] ?? 0) ?>
                    </strong>
                </div>

            </div>
        </article>

        <article class="card">
            <div class="card-header">
                <div>
                    <h2>Status do Dashboard</h2>
                    <p>Indicadores gerais do estoque.</p>
                </div>
            </div>

            <div class="report-list">

                <div class="report-list-item">
                    <span>Produtos monitorados</span>
                    <strong>
                        <?= (int) ($resumo['total_produtos'] ?? 0) ?>
                    </strong>
                </div>

                <div class="report-list-item">
                    <span>Itens críticos</span>
                    <strong>
                        <?= (int) ($resumo['produtos_criticos'] ?? 0) ?>
                    </strong>
                </div>

            </div>
        </article>

    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';