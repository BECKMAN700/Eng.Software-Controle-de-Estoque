<?php
$pageTitle = 'Painel de Relatórios';
$pageSubtitle = 'Selecione e consulte os relatórios gerenciais e financeiros de estoque.';

ob_start();
?>

<section class="page-section">
    <div class="grid grid-3">
        <!-- Card 1: Giro de Estoque -->
        <article class="card report-menu-card report-menu-card-success">
            <div class="card-header-simple">
                <h2 class="report-card-title">Giro de Estoque</h2>
                <p class="report-card-description">
                    Analise os produtos mais e menos movimentados no estoque. Classifique os itens em alto, médio ou baixo giro para otimizar compras e armazenamento.
                </p>
            </div>
            <div class="report-card-actions">
                <a href="index.php?acao=giro_estoque" class="btn btn-primary report-card-link">
                    Acessar Relatório
                </a>
            </div>
        </article>

        <!-- Card 2: Valorização do Estoque -->
        <article class="card report-menu-card report-menu-card-primary">
            <div class="card-header-simple">
                <h2 class="report-card-title">Valorização do Estoque</h2>
                <p class="report-card-description">
                    Consulte o valor financeiro total investido no seu inventário atual. Exibe a quantidade atual, preço de custo unitário e saldo financeiro total por item.
                </p>
            </div>
            <div class="report-card-actions">
                <a href="index.php?acao=valorizacao" class="btn btn-primary report-card-link">
                    Acessar Relatório
                </a>
            </div>
        </article>

        <!-- Card 3: Movimentações por Período -->
        <article class="card report-menu-card report-menu-card-warning">
            <div class="card-header-simple">
                <h2 class="report-card-title">Movimentações por Período</h2>
                <p class="report-card-description">
                    Filtre todas as entradas e saídas de mercadoria em um intervalo de datas. Perfeito para auditoria detalhada e acompanhamento de fluxos específicos.
                </p>
            </div>
            <div class="report-card-actions">
                <a href="index.php?acao=movimentacoes_periodo" class="btn btn-primary report-card-link">
                    Acessar Relatório
                </a>
            </div>
        </article>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
