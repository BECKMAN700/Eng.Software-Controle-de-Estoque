<?php
$tituloTopo = $pageTitle ?? 'Controle de Estoque';
$subtituloTopo = $pageSubtitle ?? 'Acompanhe o estoque e as movimentações do sistema.';

$acaoAtual = $_GET['acao'] ?? 'listar';
$idAtual = $_GET['id'] ?? '';
$dataAtual = date('d/m/Y');

if (!function_exists('escTopbar')) {
    function escTopbar($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

// Trilha de navegação (breadcrumbs) — cada item: [rótulo, url|null]
$inv = 'index.php?acao=inventarios';
$rel = 'index.php?acao=relatorios';
$est = 'index.php?acao=listar';
$invDet = $idAtual !== '' ? 'index.php?acao=inventario_detalhar&id=' . urlencode($idAtual) : null;

$mapaBreadcrumbs = [
    'dashboard'              => [['Dashboard', null]],
    'listar'                 => [['Estoque', null]],
    'catalogo'              => [['Estoque', $est], ['Catálogo', null]],
    'criar'                  => [['Estoque', $est], ['Cadastrar produto', null]],
    'editar'                 => [['Estoque', $est], ['Editar produto', null]],
    'movimentar'             => [['Estoque', $est], ['Movimentar', null]],
    'entrada'                => [['Estoque', $est], ['Entrada', null]],
    'saida'                  => [['Estoque', $est], ['Saída', null]],
    'detalhes_saida'         => [['Estoque', $est], ['Detalhes da saída', null]],
    'historico_movimentacoes' => [['Estoque', $est], ['Histórico de movimentações', null]],
    'divergencias'           => [['Estoque', $est], ['Divergências', null]],
    'inventarios'            => [['Inventário', null]],
    'inventario_criar'       => [['Inventário', $inv], ['Novo inventário', null]],
    'inventario_detalhar'    => [['Inventário', $inv], ['Detalhe', null]],
    'inventario_contagem'    => [['Inventário', $inv], ['Detalhe', $invDet], ['Contagem', null]],
    'inventario_divergencias' => [['Inventário', $inv], ['Detalhe', $invDet], ['Divergências', null]],
    'inventario_auditoria'   => [['Inventário', $inv], ['Detalhe', $invDet], ['Auditoria', null]],
    'relatorios'             => [['Relatórios', null]],
    'giro_estoque'           => [['Relatórios', $rel], ['Giro de estoque', null]],
    'valorizacao'            => [['Relatórios', $rel], ['Valorização', null]],
    'movimentacoes_periodo'  => [['Relatórios', $rel], ['Movimentações por período', null]],
    'usuarios'               => [['Administração', null], ['Usuários', null]],
    'usuario_criar'          => [['Administração', null], ['Usuários', 'index.php?acao=usuarios'], ['Novo usuário', null]],
];

$trilha = $mapaBreadcrumbs[$acaoAtual] ?? [];
?>

<header class="topbar">
    <div class="topbar-title">
        <?php if (count($trilha) >= 2): ?>
            <nav class="breadcrumbs" aria-label="Trilha de navegação">
                <ol>
                    <?php foreach ($trilha as $i => $crumb): ?>
                        <?php [$rotulo, $url] = $crumb; ?>
                        <li>
                            <?php if ($url !== null && $i < count($trilha) - 1): ?>
                                <a href="<?= escTopbar($url) ?>"><?= escTopbar($rotulo) ?></a>
                            <?php else: ?>
                                <span aria-current="page"><?= escTopbar($rotulo) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php else: ?>
            <div class="topbar-kicker">Sistema MVC em PHP nativo</div>
        <?php endif; ?>

        <h1><?= escTopbar($tituloTopo) ?></h1>
        <p><?= escTopbar($subtituloTopo) ?></p>
    </div>

    <div class="topbar-actions">
        <button type="button" class="global-search-trigger" data-search-open aria-label="Buscar no sistema">
            <?= uiIcon('search', 'btn-icon') ?>
            <span class="global-search-trigger-label">Buscar produto, movimentação, inventário…</span>
            <kbd class="global-search-kbd">Ctrl K</kbd>
        </button>

        <button
            type="button"
            class="theme-toggle"
            data-theme-toggle
            aria-label="Ativar tema escuro"
            title="Ativar tema escuro"
        >
            <span data-theme-icon="light"><?= uiIcon('moon', 'icon') ?></span>
            <span data-theme-icon="dark"><?= uiIcon('sun', 'icon') ?></span>
        </button>

        <div class="topbar-status">
            <span>Hoje</span>
            <strong><?= $dataAtual ?></strong>
        </div>

        <a class="btn btn-primary" href="index.php?acao=criar">
            <?= uiIcon('plus', 'btn-icon') ?>
            <span>Novo produto</span>
        </a>
    </div>
</header>
