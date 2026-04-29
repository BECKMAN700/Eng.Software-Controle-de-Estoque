<?php
$tituloTopo = $pageTitle ?? 'Controle de Estoque';
$subtituloTopo = $pageSubtitle ?? 'Acompanhe o estoque e as movimentações do sistema.';
$buscaAtual = $_GET['busca'] ?? '';
?>

<header class="topbar">
    <div class="topbar-title">
        <h1><?= htmlspecialchars($tituloTopo) ?></h1>
        <p><?= htmlspecialchars($subtituloTopo) ?></p>
    </div>

    <div class="topbar-actions">
        <form class="topbar-search" action="index.php" method="GET">
            <input type="hidden" name="acao" value="listar">
            <input
                type="text"
                name="busca"
                placeholder="Buscar produto ou código"
                value="<?= htmlspecialchars($buscaAtual) ?>"
            >
            <button type="submit">Buscar</button>
        </form>

        <a class="btn btn-primary" href="index.php?acao=criar">
            Novo produto
        </a>
    </div>
</header>