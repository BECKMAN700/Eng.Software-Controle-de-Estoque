<?php
$pageTitle = $pageTitle ?? 'Controle de Estoque';
$pageSubtitle = $pageSubtitle ?? 'Gerencie produtos, entradas, saídas e alertas de estoque.';
$content = $content ?? '';
$currentAction = $_GET['acao'] ?? 'listar';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Controle de Estoque</title>

    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/pages.css">
</head>
<body>
    <button
        type="button"
        class="sidebar-tab"
        data-sidebar-open
        aria-controls="app-sidebar"
        aria-expanded="false"
    >
        <span class="sidebar-tab-lines" aria-hidden="true"></span>
        <span>Menu</span>
    </button>

    <div class="sidebar-overlay" data-sidebar-close></div>

    <div class="app-shell">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="app-main">
            <?php require __DIR__ . '/../partials/topbar.php'; ?>

            <main class="content-area">
                <?php require __DIR__ . '/../partials/flash.php'; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <script>
        (function () {
            const body = document.body;
            const openButtons = document.querySelectorAll('[data-sidebar-open]');
            const closeButtons = document.querySelectorAll('[data-sidebar-close]');

            function setMenuState(isOpen) {
                body.classList.toggle('sidebar-open', isOpen);
                openButtons.forEach(function (button) {
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            }

            openButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setMenuState(true);
                });
            });

            closeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setMenuState(false);
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setMenuState(false);
                }
            });
        }());
    </script>
</body>
</html>
