<?php
$pageTitle = 'Painel de Relatórios';
$pageSubtitle = 'Selecione e consulte os relatórios gerenciais e financeiros de estoque.';

ob_start();
?>

<section class="page-section">
    <div class="grid grid-3">
        <!-- Card 1: Giro de Estoque -->
        <article class="card report-menu-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 220px; transition: transform 0.2s ease, box-shadow 0.2s ease; border-top: 4px solid var(--success); padding: 24px;">
            <div class="card-header-simple">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">Giro de Estoque</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.4;">
                    Analise os produtos mais e menos movimentados no estoque. Classifique os itens em alto, médio ou baixo giro para otimizar compras e armazenamento.
                </p>
            </div>
            <div class="card-actions" style="margin-top: 20px;">
                <a href="index.php?acao=giro_estoque" class="btn btn-primary" style="width: 100%; border-radius: 8px; font-weight: 600;">
                    Acessar Relatório
                </a>
            </div>
        </article>

        <!-- Card 2: Valorização do Estoque -->
        <article class="card report-menu-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 220px; transition: transform 0.2s ease, box-shadow 0.2s ease; border-top: 4px solid var(--primary); padding: 24px;">
            <div class="card-header-simple">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">Valorização do Estoque</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.4;">
                    Consulte o valor financeiro total investido no seu inventário atual. Exibe a quantidade atual, preço de custo unitário e saldo financeiro total por item.
                </p>
            </div>
            <div class="card-actions" style="margin-top: 20px;">
                <a href="index.php?acao=valorizacao" class="btn btn-primary" style="width: 100%; border-radius: 8px; font-weight: 600;">
                    Acessar Relatório
                </a>
            </div>
        </article>

        <!-- Card 3: Movimentações por Período -->
        <article class="card report-menu-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 220px; transition: transform 0.2s ease, box-shadow 0.2s ease; border-top: 4px solid var(--warning); padding: 24px;">
            <div class="card-header-simple">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">Movimentações por Período</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.4;">
                    Filtre todas as entradas e saídas de mercadoria em um intervalo de datas. Perfeito para auditoria detalhada e acompanhamento de fluxos específicos.
                </p>
            </div>
            <div class="card-actions" style="margin-top: 20px;">
                <a href="index.php?acao=movimentacoes_periodo" class="btn btn-primary" style="width: 100%; border-radius: 8px; font-weight: 600;">
                    Acessar Relatório
                </a>
            </div>
        </article>
    </div>
</section>

<style>
.report-menu-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(16, 24, 40, 0.12) !important;
}
</style>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
