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
    <div class="grid grid-4">

        <article class="metric-card summary-card-success">
            <p class="metric-label">Entradas nos últimos 30 dias</p>
            <strong class="metric-value">
                <?= (int) ($entradasSaidas['entrada'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Total de unidades adicionadas ao estoque.
            </p>
        </article>

        <article class="metric-card summary-card-danger">
            <p class="metric-label">Saídas nos últimos 30 dias</p>
            <strong class="metric-value">
                <?= (int) ($entradasSaidas['saida'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Total de unidades retiradas do estoque.
            </p>
        </article>

        <article class="metric-card">
            <p class="metric-label">Produtos monitorados</p>
            <strong class="metric-value">
                <?= (int) ($resumo['total_produtos'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Produtos considerados nos indicadores.
            </p>
        </article>

        <article class="metric-card summary-card-warning">
            <p class="metric-label">Itens críticos</p>
            <strong class="metric-value">
                <?= (int) ($resumo['produtos_criticos'] ?? 0) ?>
            </strong>
            <p class="metric-description">
                Produtos abaixo do mínimo configurado.
            </p>
        </article>

    </div>
</section>


<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Entradas x Saídas</h2>
                <p>Comparativo das movimentações registradas nos últimos 30 dias.</p>
            </div>
        </div>

        <canvas
            id="graficoEntradasSaidas"
            height="110"
            aria-label="Gráfico de entradas e saídas dos últimos 30 dias"
            role="img"
        ></canvas>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Produtos Mais Movimentados</h2>
                <p>Produtos com maior volume de movimentação registrado.</p>
            </div>
        </div>


        <canvas
            id="graficoMaisMovimentados"
            height="120"
        ></canvas>
    </div>
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Tendência de Movimentações</h2>
                <p>Evolução de entradas e saídas nos últimos 7 dias.</p>
            </div>
        </div>

        <canvas
            id="graficoTendenciaMovimentacoes"
            height="120"
        ></canvas>
    </div>
    
</section>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Produtos Críticos</h2>
                <p>Itens abaixo do estoque mínimo que exigem atenção.</p>
            </div>
        </div>

        <?php if (empty($produtosCriticos)): ?>
            <div class="empty-state">
                <p>Nenhum produto crítico encontrado.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Atual</th>
                            <th>Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtosCriticos as $produto): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) ($produto['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) $produto['quantidade'] ?></td>
                                <td><?= (int) $produto['estoque_minimo'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const tendenciaBruta = <?= json_encode($tendenciaMovimentacoes ?? []) ?>;

const datasTendencia = [...new Set(tendenciaBruta.map(item => item.data))];

const entradasTendencia = datasTendencia.map(data => {
    const item = tendenciaBruta.find(linha => linha.data === data && linha.tipo === 'entrada');
    return item ? parseInt(item.total) : 0;
});

const saidasTendencia = datasTendencia.map(data => {
    const item = tendenciaBruta.find(linha => linha.data === data && linha.tipo === 'saida');
    return item ? parseInt(item.total) : 0;
});

const graficoTendenciaMovimentacoes =
    document.getElementById('graficoTendenciaMovimentacoes');

if (graficoTendenciaMovimentacoes && typeof Chart !== 'undefined') {
    new Chart(graficoTendenciaMovimentacoes, {
        type: 'line',
        data: {
            labels: datasTendencia,
            datasets: [
                {
                    label: 'Entradas',
                    data: entradasTendencia,
                    tension: 0.3
                },
                {
                    label: 'Saídas',
                    data: saidasTendencia,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}
</script>



<script>
    const entradasSaidasDashboard = {
        entradas: <?= (int) ($entradasSaidas['entrada'] ?? 0) ?>,
        saidas: <?= (int) ($entradasSaidas['saida'] ?? 0) ?>
    };

    const graficoEntradasSaidas = document.getElementById('graficoEntradasSaidas');

    if (graficoEntradasSaidas && typeof Chart !== 'undefined') {
        new Chart(graficoEntradasSaidas, {
            type: 'bar',
            data: {
                labels: ['Entradas', 'Saídas'],
                datasets: [{
                    label: 'Quantidade movimentada',
                    data: [
                        entradasSaidasDashboard.entradas,
                        entradasSaidasDashboard.saidas
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
const produtosMovimentados = <?= json_encode(
    array_map(
        fn($produto) => $produto['nome'],
        $maisMovimentados ?? []
    )
) ?>;

const valoresMovimentados = <?= json_encode(
    array_map(
        fn($produto) => (int) $produto['total_movimentado'],
        $maisMovimentados ?? []
    )
) ?>;

const graficoMaisMovimentados =
    document.getElementById('graficoMaisMovimentados');

if (graficoMaisMovimentados && typeof Chart !== 'undefined') {

    new Chart(graficoMaisMovimentados, {
        type: 'bar',
        data: {
            labels: produtosMovimentados,
            datasets: [{
                label: 'Movimentações',
                data: valoresMovimentados
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y'
        }
    });
}
</script>
<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';