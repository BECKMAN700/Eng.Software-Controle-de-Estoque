<?php

$pageTitle = 'Dashboard Gerencial';
$pageSubtitle = 'Indicadores estratégicos do estoque em tempo real.';

$resumo = $resumo ?? [];
$comparativo = $comparativo ?? ['atual' => ['entrada' => 0, 'saida' => 0], 'anterior' => ['entrada' => 0, 'saida' => 0]];
$produtosCriticos = $produtosCriticos ?? [];
$maisMovimentados = $maisMovimentados ?? [];
$tendenciaMovimentacoes = $tendenciaMovimentacoes ?? [];

if (!function_exists('formatarDinheiro')) {
    function formatarDinheiro($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Calcula a variação percentual do período atual vs. o anterior.
 * @return array{dir: string, pct: int}
 */
if (!function_exists('tendenciaKpi')) {
    function tendenciaKpi(int $atual, int $anterior): array
    {
        if ($anterior <= 0) {
            return ['dir' => $atual > 0 ? 'up' : 'flat', 'pct' => $atual > 0 ? 100 : 0];
        }
        $pct = (int) round((($atual - $anterior) / $anterior) * 100);
        return ['dir' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat'), 'pct' => abs($pct)];
    }
}

$entradas = (int) ($comparativo['atual']['entrada'] ?? 0);
$saidas = (int) ($comparativo['atual']['saida'] ?? 0);
$entradasAnt = (int) ($comparativo['anterior']['entrada'] ?? 0);
$saidasAnt = (int) ($comparativo['anterior']['saida'] ?? 0);

$tEntradas = tendenciaKpi($entradas, $entradasAnt);
$tSaidas = tendenciaKpi($saidas, $saidasAnt);

$setas = ['up' => '▲', 'down' => '▼', 'flat' => '–'];
$criticos = (int) ($resumo['produtos_criticos'] ?? 0);

ob_start();
?>

<!-- KPIs: hierarquia com destaque para o valor do estoque e itens críticos -->
<section class="page-section">
    <div class="dashboard-kpis">
        <article class="card kpi-card kpi-hero">
            <p class="kpi-label">Valor do estoque</p>
            <strong class="kpi-value"><?= formatarDinheiro($resumo['valor_total_estoque'] ?? 0) ?></strong>
            <p class="kpi-sub">
                <?= (int) ($resumo['total_produtos'] ?? 0) ?> produtos ·
                <?= number_format((int) ($resumo['total_unidades'] ?? 0), 0, ',', '.') ?> unidades
            </p>
        </article>

        <article class="card kpi-card <?= $criticos > 0 ? 'kpi-danger' : '' ?>">
            <p class="kpi-label">Produtos críticos</p>
            <strong class="kpi-value"><?= $criticos ?></strong>
            <p class="kpi-sub">Abaixo do estoque mínimo</p>
        </article>

        <article class="card kpi-card">
            <p class="kpi-label">Entradas (30 dias)</p>
            <strong class="kpi-value"><?= number_format($entradas, 0, ',', '.') ?></strong>
            <span class="kpi-trend kpi-trend-<?= $tEntradas['dir'] ?>">
                <?= $setas[$tEntradas['dir']] ?> <?= $tEntradas['pct'] ?>%
                <small>vs. 30 dias anteriores</small>
            </span>
        </article>

        <article class="card kpi-card">
            <p class="kpi-label">Saídas (30 dias)</p>
            <strong class="kpi-value"><?= number_format($saidas, 0, ',', '.') ?></strong>
            <span class="kpi-trend kpi-trend-neutral">
                <?= $setas[$tSaidas['dir']] ?> <?= $tSaidas['pct'] ?>%
                <small>vs. 30 dias anteriores</small>
            </span>
        </article>
    </div>
</section>

<!-- Gráfico de tendência + lista do que precisa de atenção -->
<section class="page-section">
    <div class="dashboard-grid-2">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Tendência de movimentações</h2>
                    <p>Entradas e saídas dos últimos 7 dias.</p>
                </div>
            </div>
            <div class="chart-box">
                <canvas id="graficoTendencia" role="img" aria-label="Tendência de movimentações nos últimos 7 dias"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Produtos críticos</h2>
                    <p>Itens abaixo do mínimo.</p>
                </div>
            </div>

            <?php if (empty($produtosCriticos)): ?>
                <div class="empty-state">Nenhum produto crítico no momento. 👍</div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table table-slim">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="numeric">Atual</th>
                                <th class="numeric">Mín.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtosCriticos as $produto): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?acao=entrada&id=<?= (int) ($produto['id'] ?? 0) ?>" class="critico-nome">
                                            <?= esc($produto['nome'] ?? '') ?>
                                        </a>
                                    </td>
                                    <td class="numeric">
                                        <span class="stock-pill situacao-critico"><?= (int) $produto['quantidade'] ?></span>
                                    </td>
                                    <td class="numeric"><?= (int) $produto['estoque_minimo'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Produtos mais movimentados -->
<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Produtos mais movimentados</h2>
                <p>Maior volume de movimentação registrado.</p>
            </div>
        </div>
        <div class="chart-box chart-box-lg">
            <canvas id="graficoMaisMovimentados" role="img" aria-label="Produtos mais movimentados"></canvas>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script>
    (function () {
        var dadosTendencia = <?= json_encode($tendenciaMovimentacoes ?? [], JSON_UNESCAPED_UNICODE) ?>;
        var maisMovimentados = <?= json_encode(array_map(function ($p) {
            return ['nome' => $p['nome'] ?? '', 'total' => (int) ($p['total_movimentado'] ?? 0)];
        }, $maisMovimentados ?? []), JSON_UNESCAPED_UNICODE) ?>;

        function iniciar() {
            if (typeof Chart === 'undefined') {
                return window.setTimeout(iniciar, 120);
            }

            var css = getComputedStyle(document.documentElement);
            function cor(nome, alt) { return (css.getPropertyValue(nome) || '').trim() || alt; }

            var corTexto = cor('--text-muted', '#667085');
            var corGrade = cor('--border', '#e5e7eb');
            var MARCA = '#2563eb';
            var DESTAQUE = '#14b8a6';
            var nf = new Intl.NumberFormat('pt-BR');

            Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
            Chart.defaults.color = corTexto;

            function eixo(extra) {
                return Object.assign({
                    grid: { color: corGrade, drawBorder: false },
                    border: { display: false },
                    ticks: { color: corTexto, precision: 0, callback: function (v) { return nf.format(v); } }
                }, extra || {});
            }

            function tooltip() {
                return {
                    backgroundColor: 'rgba(16,24,40,.92)',
                    titleColor: '#fff', bodyColor: '#fff',
                    padding: 10, cornerRadius: 8, usePointStyle: true,
                    callbacks: {
                        label: function (ctx) {
                            var val = ctx.parsed.y;
                            if (val === undefined || val === null) { val = ctx.parsed.x; }
                            return ' ' + (ctx.dataset.label || '') + ': ' + nf.format(val);
                        }
                    }
                };
            }

            function vazio(id, mensagem) {
                var canvas = document.getElementById(id);
                if (canvas) {
                    var box = canvas.closest('.chart-box') || canvas.parentNode;
                    box.innerHTML = '<div class="empty-state">' + mensagem + '</div>';
                }
            }

            // ── Tendência (linha, 7 dias) ────────────────────────────────
            var datas = dadosTendencia.map(function (i) { return i.data; }).filter(function (v, idx, arr) { return arr.indexOf(v) === idx; });
            if (datas.length === 0) {
                vazio('graficoTendencia', 'Sem movimentações no período.');
            } else {
                function serie(tipo) {
                    return datas.map(function (d) {
                        var item = dadosTendencia.find(function (l) { return l.data === d && l.tipo === tipo; });
                        return item ? parseInt(item.total, 10) : 0;
                    });
                }
                var rotulos = datas.map(function (d) {
                    var p = String(d).split('-');
                    return p.length === 3 ? p[2] + '/' + p[1] : d;
                });

                new Chart(document.getElementById('graficoTendencia'), {
                    type: 'line',
                    data: {
                        labels: rotulos,
                        datasets: [
                            { label: 'Entradas', data: serie('entrada'), borderColor: MARCA, backgroundColor: 'rgba(37,99,235,.12)', fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: MARCA, borderWidth: 2 },
                            { label: 'Saídas', data: serie('saida'), borderColor: DESTAQUE, backgroundColor: 'rgba(20,184,166,.12)', fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: DESTAQUE, borderWidth: 2 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { labels: { color: corTexto, usePointStyle: true, boxWidth: 8, padding: 16 } },
                            tooltip: tooltip()
                        },
                        scales: { x: eixo(), y: eixo({ beginAtZero: true }) }
                    }
                });
            }

            // ── Mais movimentados (barra horizontal) ─────────────────────
            if (!maisMovimentados.length || maisMovimentados.every(function (p) { return p.total === 0; })) {
                vazio('graficoMaisMovimentados', 'Sem movimentações registradas ainda.');
            } else {
                new Chart(document.getElementById('graficoMaisMovimentados'), {
                    type: 'bar',
                    data: {
                        labels: maisMovimentados.map(function (p) { return p.nome; }),
                        datasets: [{
                            label: 'Movimentações',
                            data: maisMovimentados.map(function (p) { return p.total; }),
                            backgroundColor: MARCA, hoverBackgroundColor: DESTAQUE,
                            borderRadius: 8, borderSkipped: false, maxBarThickness: 26
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: tooltip() },
                        scales: { x: eixo({ beginAtZero: true }), y: eixo() }
                    }
                });
            }
        }

        iniciar();
    }());
</script>
<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
