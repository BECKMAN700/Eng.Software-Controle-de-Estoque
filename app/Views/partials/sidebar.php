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

        <?php if (Auth::isAdmin()): ?>
        <a class="nav-link <?= menuAtivo(['criar']) ?>" href="index.php?acao=criar">
            <span class="nav-icon"></span>
            <span>Cadastrar produto</span>
        </a>
        <?php endif; ?>

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
            <span>Acoes de estoque</span>
        </a>

        <span class="nav-section-title">Inventário</span>

        <a class="nav-link <?= menuAtivo(['inventarios', 'inventario_criar', 'inventario_detalhar', 'inventario_contagem', 'inventario_divergencias', 'inventario_auditoria']) ?>" href="index.php?acao=inventarios">
            <span class="nav-icon"></span>
            <span>Inventários</span>
        </a>
        <?php if (Auth::isAdmin()): ?>
        <span class="nav-section-title">Administracao</span>

        <a class="nav-link <?= menuAtivo(['usuarios', 'usuario_criar']) ?>" href="index.php?acao=usuarios">
            <span class="nav-icon"></span>
            <span>Usuarios</span>
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
                Sair
            </button>
        </form>
    </div>
</aside>
