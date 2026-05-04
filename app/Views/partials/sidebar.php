<?php
if (!function_exists('menuAtivo')) {
    function menuAtivo(array $acoes): string
    {
        $acaoAtual = $_GET['acao'] ?? 'listar';
        return in_array($acaoAtual, $acoes, true) ? 'active' : '';
    }
}

$nomeUsuario = Sessao::getNome();
$papelUsuario = Sessao::getPapel();
$papelLabel   = $papelUsuario === 'admin' ? 'Administrador' : 'Estoquista';
?>

<aside class="sidebar" id="app-sidebar" aria-label="Menu principal">
    <div class="sidebar-brand">
        <div class="brand-mark">CE</div>

        <div class="brand-text">
            <strong>Controle</strong>
            <span>de Estoque</span>
        </div>

        <button type="button" class="sidebar-close" data-sidebar-close aria-label="Fechar menu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </button>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section-title">Principal</span>

        <a class="nav-link <?= menuAtivo(['listar']) ?>" href="index.php?acao=listar">
            <span class="nav-icon"></span>
            <span>Painel de estoque</span>
        </a>

        <a class="nav-link <?= menuAtivo(['catalogo']) ?>" href="index.php?acao=catalogo">
            <span class="nav-icon"></span>
            <span>Catálogo de produtos</span>
        </a>

        <a class="nav-link <?= menuAtivo(['relatorios']) ?>" href="index.php?acao=relatorios">
            <span class="nav-icon"></span>
            <span>Relatórios</span>
        </a>

        <span class="nav-section-title">Produtos</span>

        <a class="nav-link <?= menuAtivo(['criar']) ?>" href="index.php?acao=criar">
            <span class="nav-icon"></span>
            <span>Cadastrar produto</span>
        </a>

        <a class="nav-link" href="index.php?acao=listar#produtos">
            <span class="nav-icon"></span>
            <span>Lista completa</span>
        </a>

        <a class="nav-link" href="index.php?acao=listar#alertas-estoque">
            <span class="nav-icon"></span>
            <span>Alertas de estoque</span>
        </a>

        <span class="nav-section-title">Movimentações</span>

        <a class="nav-link" href="index.php?acao=listar#movimentacoes">
            <span class="nav-icon"></span>
            <span>Histórico e ações</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <span class="sidebar-user-nome"><?= htmlspecialchars($nomeUsuario) ?></span>
            <span class="sidebar-user-papel"><?= htmlspecialchars($papelLabel) ?></span>
        </div>

        <a class="btn-logout" href="index.php?acao=logout" title="Sair do sistema">
            Sair
        </a>
    </div>
</aside>
