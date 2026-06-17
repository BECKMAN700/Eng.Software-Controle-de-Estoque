<?php
$pageTitle = $pageTitle ?? 'Controle de Estoque';
$pageSubtitle = $pageSubtitle ?? 'Gerencie produtos, entradas, saídas e alertas de estoque.';
$content = $content ?? '';
$currentAction = $_GET['acao'] ?? 'listar';
$assetVersion = '20260617-design';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Controle de Estoque</title>

    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg?v=<?= $assetVersion ?>">
    <meta name="theme-color" content="#2563eb">
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('ce-theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = savedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        }());
    </script>
    <link rel="stylesheet" href="assets/css/base.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/layout.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/pages.css?v=<?= $assetVersion ?>">
</head>
<body>
    <?php require __DIR__ . '/../partials/icons.php'; ?>

    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

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

            <main class="content-area" id="conteudo" tabindex="-1">
                <?php require __DIR__ . '/../partials/flash.php'; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <script>
        (function () {
            var body = document.body;
            var openButtons = document.querySelectorAll('[data-sidebar-open]');
            var themeButtons = document.querySelectorAll('[data-theme-toggle]');

            function setMenuState(isOpen) {
                body.classList.toggle('sidebar-open', isOpen);

                openButtons.forEach(function (button) {
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    button.setAttribute('aria-label', isOpen ? 'Fechar menu' : 'Abrir menu');
                });
            }

            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);

                try {
                    localStorage.setItem('ce-theme', theme);
                } catch (error) {
                    // Theme still works without localStorage; it just will not persist.
                }

                themeButtons.forEach(function (button) {
                    var isDark = theme === 'dark';
                    button.setAttribute('aria-label', isDark ? 'Ativar tema claro' : 'Ativar tema escuro');
                    button.setAttribute('title', isDark ? 'Ativar tema claro' : 'Ativar tema escuro');
                });
            }

            setTheme(document.documentElement.getAttribute('data-theme') || 'light');

            document.addEventListener('click', function (event) {
                var themeTarget = event.target.closest('[data-theme-toggle]');
                var openTarget = event.target.closest('[data-sidebar-open]');
                var closeTarget = event.target.closest('[data-sidebar-close], .sidebar .nav-link');

                if (themeTarget) {
                    var currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    setTheme(currentTheme === 'dark' ? 'light' : 'dark');
                    return;
                }

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
