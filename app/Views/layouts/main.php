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
        class="menu-toggle"
        data-sidebar-open
        aria-controls="app-sidebar"
        aria-expanded="false"
        aria-label="Abrir menu"
    >
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
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
            var body = document.body;
            var openButtons = document.querySelectorAll('[data-sidebar-open]');

            function setMenuState(isOpen) {
                body.classList.toggle('sidebar-open', isOpen);

                openButtons.forEach(function (button) {
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    button.setAttribute('aria-label', isOpen ? 'Fechar menu' : 'Abrir menu');
                });
            }

            document.addEventListener('click', function (event) {
                var openTarget = event.target.closest('[data-sidebar-open]');
                var closeTarget = event.target.closest('[data-sidebar-close], .sidebar .nav-link');

                if (openTarget) {
                    setMenuState(true);
                    return;
                }

                if (closeTarget) {
                    setMenuState(false);
                }
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
