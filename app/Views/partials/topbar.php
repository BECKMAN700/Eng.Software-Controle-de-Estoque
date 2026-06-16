<?php
$tituloTopo = $pageTitle ?? 'Controle de Estoque';
$subtituloTopo = $pageSubtitle ?? 'Acompanhe o estoque e as movimentações do sistema.';

$acaoAtual = $_GET['acao'] ?? 'listar';
$buscaAtual = $_GET['busca'] ?? '';

$acaoBusca = $acaoAtual === 'catalogo' ? 'catalogo' : 'listar';
$dataAtual = date('d/m/Y');
?>

<header class="topbar">
    <div class="topbar-title">
        <div class="topbar-kicker">
            Sistema MVC em PHP nativo
        </div>

        <h1><?= htmlspecialchars($tituloTopo) ?></h1>
        <p><?= htmlspecialchars($subtituloTopo) ?></p>
    </div>

    <div class="topbar-actions">
        <form class="topbar-search" action="index.php" method="GET">
            <input type="hidden" name="acao" value="<?= htmlspecialchars($acaoBusca) ?>">

            <input
                type="text"
                name="busca"
                placeholder="Buscar produto ou código"
                value="<?= htmlspecialchars($buscaAtual) ?>"
            >

            <button type="submit">
                <?= uiIcon('search', 'btn-icon') ?>
                <span>Buscar</span>
            </button>
        </form>

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
