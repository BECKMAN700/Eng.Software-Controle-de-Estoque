<?php
$pageTitle = $pageTitle ?? 'Controle de Estoque';
$pageSubtitle = $pageSubtitle ?? 'Gerencie produtos, entradas, saídas e alertas de estoque.';
$content = $content ?? '';
$currentAction = $_GET['acao'] ?? 'listar';
$assetVersion = '20260623-integracao';
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

    <div class="search-palette" data-search-palette hidden>
        <div class="search-palette-backdrop" data-search-close></div>
        <div class="search-palette-dialog" role="dialog" aria-modal="true" aria-label="Busca global">
            <div class="search-palette-field">
                <?= uiIcon('search', 'icon') ?>
                <input
                    type="text"
                    class="search-palette-input"
                    data-search-input
                    placeholder="Buscar produto, movimentação, inventário…"
                    autocomplete="off"
                    spellcheck="false"
                >
                <kbd>Esc</kbd>
            </div>
            <div class="search-palette-results" data-search-results>
                <p class="search-palette-hint">Digite ao menos 2 caracteres para buscar.</p>
            </div>
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

            var docEl = document.documentElement;

            function setNavExpanded(expanded) {
                docEl.classList.toggle('nav-expanded', expanded);
            }

            document.addEventListener('click', function (event) {
                var themeTarget = event.target.closest('[data-theme-toggle]');
                var railTarget = event.target.closest('[data-rail-toggle]');
                var openTarget = event.target.closest('[data-sidebar-open]');
                var closeTarget = event.target.closest('[data-sidebar-close], .sidebar .nav-link');

                if (themeTarget) {
                    var currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    setTheme(currentTheme === 'dark' ? 'light' : 'dark');
                    return;
                }

                if (railTarget) {
                    // Expande/recolhe o trilho no desktop (sobreposto, transitório)
                    setNavExpanded(!docEl.classList.contains('nav-expanded'));
                    return;
                }

                if (openTarget) {
                    setMenuState(true);
                    return;
                }

                if (closeTarget) {
                    setMenuState(false);
                    setNavExpanded(false);
                    return;
                }

                // Clique fora do menu expandido recolhe o trilho
                if (docEl.classList.contains('nav-expanded') && !event.target.closest('.sidebar')) {
                    setNavExpanded(false);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setMenuState(false);
                    setNavExpanded(false);
                }
            });
        }());

        // ── Paleta de busca global (Ctrl+K / "/") ────────────────────────────
        (function () {
            var palette = document.querySelector('[data-search-palette]');
            if (!palette) {
                return;
            }

            var input = palette.querySelector('[data-search-input]');
            var results = palette.querySelector('[data-search-results]');
            var debounce = null;
            var ultimoTermo = '';

            function abrir() {
                palette.hidden = false;
                document.body.classList.add('search-open');
                window.requestAnimationFrame(function () {
                    input.focus();
                    input.select();
                });
            }

            function fechar() {
                palette.hidden = true;
                document.body.classList.remove('search-open');
            }

            function estaAberta() {
                return !palette.hidden;
            }

            function escapeHtml(texto) {
                var div = document.createElement('div');
                div.textContent = texto == null ? '' : String(texto);
                return div.innerHTML;
            }

            function renderHint(texto) {
                results.innerHTML = '<p class="search-palette-hint">' + escapeHtml(texto) + '</p>';
            }

            function renderGrupos(grupos) {
                if (!grupos || grupos.length === 0) {
                    renderHint('Nenhum resultado encontrado.');
                    return;
                }

                var html = '';
                grupos.forEach(function (grupo) {
                    html += '<div class="search-palette-group">';
                    html += '<span class="search-palette-group-label">' + escapeHtml(grupo.rotulo) + '</span>';
                    grupo.itens.forEach(function (item) {
                        html += '<a class="search-palette-item" href="' + escapeHtml(item.url) + '">';
                        html += '<span class="search-palette-item-title">' + escapeHtml(item.titulo) + '</span>';
                        if (item.subtitulo) {
                            html += '<span class="search-palette-item-sub">' + escapeHtml(item.subtitulo) + '</span>';
                        }
                        html += '</a>';
                    });
                    html += '</div>';
                });
                results.innerHTML = html;
            }

            function buscar(termo) {
                fetch('index.php?acao=busca_global&q=' + encodeURIComponent(termo), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (resposta) { return resposta.json(); })
                    .then(function (dados) {
                        if (termo !== ultimoTermo) {
                            return;
                        }
                        renderGrupos(dados.grupos);
                    })
                    .catch(function () {
                        renderHint('Não foi possível buscar agora. Tente novamente.');
                    });
            }

            input.addEventListener('input', function () {
                var termo = input.value.trim();
                ultimoTermo = termo;
                window.clearTimeout(debounce);

                if (termo.length < 2) {
                    renderHint('Digite ao menos 2 caracteres para buscar.');
                    return;
                }

                renderHint('Buscando…');
                debounce = window.setTimeout(function () { buscar(termo); }, 220);
            });

            palette.addEventListener('click', function (event) {
                if (event.target.closest('[data-search-close]')) {
                    fechar();
                }
            });

            document.addEventListener('click', function (event) {
                if (event.target.closest('[data-search-open]')) {
                    abrir();
                }
            });

            document.addEventListener('keydown', function (event) {
                var meta = event.ctrlKey || event.metaKey;

                if (meta && (event.key === 'k' || event.key === 'K')) {
                    event.preventDefault();
                    estaAberta() ? fechar() : abrir();
                    return;
                }

                var emCampo = /^(input|textarea|select)$/i.test(event.target.tagName) || event.target.isContentEditable;
                if (event.key === '/' && !emCampo && !estaAberta()) {
                    event.preventDefault();
                    abrir();
                    return;
                }

                if (event.key === 'Escape' && estaAberta()) {
                    fechar();
                }
            });
        }());
    </script>
    <script src="assets/js/tables.js?v=<?= $assetVersion ?>" defer></script>
    <script src="assets/js/forms.js?v=<?= $assetVersion ?>" defer></script>
</body>
</html>
