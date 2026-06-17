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
        <div class="brand-mark">
            <img src="assets/img/logo.svg" alt="Controle de Estoque" width="44" height="44">
        </div>

        <div class="brand-text">
            <strong>Controle</strong>
            <span>de Estoque</span>
        </div>

        <button type="button" class="sidebar-close" data-sidebar-close aria-label="Fechar menu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </button>
    </div>

    <button type="button" class="rail-toggle" data-rail-toggle aria-label="Expandir ou recolher o menu" title="Expandir ou recolher o menu">
        <?= uiIcon('chevron-right', 'icon') ?>
        <span class="rail-toggle-label">Recolher menu</span>
    </button>

    <nav class="sidebar-nav">
        <span class="nav-section-title">Geral</span>
        <a class="nav-link <?= menuAtivo(['dashboard']) ?>" href="index.php?acao=dashboard" title="Dashboard">
            <?= uiIcon('dashboard', 'nav-icon') ?>
            <span>Dashboard</span>
        </a>

        <span class="nav-section-title">Estoque</span>
        <a class="nav-link <?= menuAtivo(['listar']) ?>" href="index.php?acao=listar" title="Painel de estoque">
            <?= uiIcon('box', 'nav-icon') ?>
            <span>Painel de estoque</span>
        </a>

        <a class="nav-link <?= menuAtivo(['catalogo']) ?>" href="index.php?acao=catalogo" title="Catálogo de produtos">
            <?= uiIcon('catalog', 'nav-icon') ?>
            <span>Catálogo de produtos</span>
        </a>

        <?php if (Auth::isAdmin()): ?>
        <a class="nav-link <?= menuAtivo(['criar']) ?>" href="index.php?acao=criar" title="Cadastrar produto">
            <?= uiIcon('package-plus', 'nav-icon') ?>
            <span>Cadastrar produto</span>
        </a>
        <?php endif; ?>

        <a class="nav-link" href="index.php?acao=listar#alertas-estoque" title="Alertas de estoque">
            <?= uiIcon('alert', 'nav-icon') ?>
            <span>Alertas de estoque</span>
        </a>

        <span class="nav-section-title">Movimentações</span>
        <a class="nav-link" href="index.php?acao=listar#movimentacoes" title="Ações de estoque">
            <?= uiIcon('movement', 'nav-icon') ?>
            <span>Ações de estoque</span>
        </a>

        <span class="nav-section-title">Inventário</span>
        <a class="nav-link <?= menuAtivo(['inventarios', 'inventario_criar', 'inventario_detalhar', 'inventario_contagem', 'inventario_divergencias', 'inventario_auditoria']) ?>" href="index.php?acao=inventarios" title="Inventários">
            <?= uiIcon('inventory', 'nav-icon') ?>
            <span>Inventários</span>
        </a>

        <span class="nav-section-title">Relatórios</span>
        <a class="nav-link <?= menuAtivo(['relatorios', 'giro_estoque', 'valorizacao', 'movimentacoes_periodo']) ?>" href="index.php?acao=relatorios" title="Relatórios">
            <?= uiIcon('reports', 'nav-icon') ?>
            <span>Relatórios</span>
        </a>

        <?php if (Auth::isAdmin()): ?>
        <span class="nav-section-title">Administração</span>
        <a class="nav-link <?= menuAtivo(['usuarios', 'usuario_criar']) ?>" href="index.php?acao=usuarios" title="Usuários">
            <?= uiIcon('users', 'nav-icon') ?>
            <span>Usuários</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <span class="sidebar-user-nome"><?= htmlspecialchars($nomeUsuario) ?></span>
            <span class="sidebar-user-papel"><?= htmlspecialchars($papelLabel) ?></span>
        </div>

        <form class="inline-form" action="index.php?acao=logout" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Sessao::getCsrfToken()) ?>">
            <button type="submit" class="btn-logout" title="Sair do sistema">
                <?= uiIcon('logout', 'btn-icon') ?>
                <span>Sair</span>
            </button>
        </form>
    </div>
</aside>
