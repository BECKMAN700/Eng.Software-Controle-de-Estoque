<?php
if (!function_exists('menuAtivo')) {
    function menuAtivo(array $acoes): string
    {
        $acaoAtual = $_GET['acao'] ?? 'listar';
        return in_array($acaoAtual, $acoes, true) ? 'active' : '';
    }
}
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-mark">CE</div>

        <div class="brand-text">
            <strong>Controle</strong>
            <span>de Estoque</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="nav-link <?= menuAtivo(['listar']) ?>" href="index.php?acao=listar">
            <span class="nav-icon"></span>
            <span>Painel de estoque</span>
        </a>

        <a class="nav-link <?= menuAtivo(['criar']) ?>" href="index.php?acao=criar">
            <span class="nav-icon"></span>
            <span>Cadastrar produto</span>
        </a>

        <a class="nav-link" href="index.php?acao=listar#produtos">
            <span class="nav-icon"></span>
            <span>Produtos</span>
        </a>

        <a class="nav-link" href="index.php?acao=listar#alertas-estoque">
            <span class="nav-icon"></span>
            <span>Alertas de estoque</span>
        </a>

        <a class="nav-link" href="index.php?acao=listar#movimentacoes">
            <span class="nav-icon"></span>
            <span>Movimentações</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <span class="sidebar-footer-label">Projeto acadêmico</span>
        <strong>Engenharia de Software</strong>
    </div>
</aside>