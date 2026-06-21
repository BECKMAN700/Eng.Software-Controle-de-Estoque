/**
 * Aprimoramentos de tabela: menu kebab por linha, ordenação por coluna,
 * paginação no cliente e alternância de densidade.
 * Ativado por atributos data-* — não depende de framework.
 */
(function () {
    'use strict';

    // ── Menu de ações por linha (kebab) ──────────────────────────────────
    function fecharMenus(exceto) {
        document.querySelectorAll('.row-menu.open').forEach(function (menu) {
            if (menu === exceto) {
                return;
            }
            menu.classList.remove('open');
            var btn = menu.querySelector('[data-row-menu]');
            if (btn) {
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('click', function (event) {
        var botao = event.target.closest('[data-row-menu]');

        if (botao) {
            event.preventDefault();
            var menu = botao.closest('.row-menu');
            var abrir = !menu.classList.contains('open');
            fecharMenus(menu);

            if (abrir) {
                menu.classList.add('open');
                botao.setAttribute('aria-expanded', 'true');
                // Posição fixa (a tabela tem overflow que recortaria o menu)
                var lista = menu.querySelector('.row-menu-list');
                var r = botao.getBoundingClientRect();
                lista.style.top = Math.round(r.bottom + 6) + 'px';
                lista.style.left = Math.round(Math.max(8, r.right - lista.offsetWidth)) + 'px';
            } else {
                menu.classList.remove('open');
                botao.setAttribute('aria-expanded', 'false');
            }
            return;
        }

        if (!event.target.closest('.row-menu-list')) {
            fecharMenus(null);
        }
    });

    window.addEventListener('scroll', function () { fecharMenus(null); }, true);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            fecharMenus(null);
        }
    });

    // ── Tabelas aprimoradas (ordenação + paginação) ──────────────────────
    document.querySelectorAll('[data-table-enhanced]').forEach(function (tabela) {
        var corpo = tabela.tBodies[0];
        if (!corpo) {
            return;
        }

        var linhas = Array.prototype.slice.call(corpo.rows);
        var tamanhoPagina = parseInt(tabela.getAttribute('data-page-size') || '0', 10);
        var paginaAtual = 1;
        var colAtual = -1;
        var asc = true;

        var card = tabela.closest('.card') || tabela.parentNode;
        var paginador = card ? card.querySelector('[data-pagination]') : null;

        function valor(linha, idx, tipo) {
            var celula = linha.cells[idx];
            var texto = (celula ? celula.textContent : '').trim();
            if (tipo === 'num') {
                var n = parseFloat(texto.replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.'));
                return isNaN(n) ? -Infinity : n;
            }
            return texto.toLowerCase();
        }

        function render() {
            var total = linhas.length;
            var inicio = 0;
            var fim = total;

            if (tamanhoPagina > 0) {
                var totalPaginas = Math.max(1, Math.ceil(total / tamanhoPagina));
                if (paginaAtual > totalPaginas) {
                    paginaAtual = totalPaginas;
                }
                inicio = (paginaAtual - 1) * tamanhoPagina;
                fim = Math.min(inicio + tamanhoPagina, total);

                if (paginador) {
                    paginador.querySelector('[data-page-info]').textContent =
                        'Página ' + paginaAtual + ' de ' + totalPaginas + ' · ' + total + ' itens';
                    paginador.querySelector('[data-page-prev]').disabled = paginaAtual <= 1;
                    paginador.querySelector('[data-page-next]').disabled = paginaAtual >= totalPaginas;
                }
            }

            linhas.forEach(function (linha, i) {
                linha.style.display = (i >= inicio && i < fim) ? '' : 'none';
            });
        }

        function ordenar(idx, tipo) {
            if (colAtual === idx) {
                asc = !asc;
            } else {
                colAtual = idx;
                asc = true;
            }

            linhas.sort(function (a, b) {
                var va = valor(a, idx, tipo);
                var vb = valor(b, idx, tipo);
                if (va < vb) { return asc ? -1 : 1; }
                if (va > vb) { return asc ? 1 : -1; }
                return 0;
            });

            linhas.forEach(function (linha) { corpo.appendChild(linha); });

            tabela.querySelectorAll('thead th').forEach(function (th) {
                th.removeAttribute('data-sort-dir');
            });
            var th = tabela.querySelectorAll('thead th')[idx];
            if (th) {
                th.setAttribute('data-sort-dir', asc ? 'asc' : 'desc');
            }

            paginaAtual = 1;
            render();
        }

        tabela.querySelectorAll('thead th[data-sort]').forEach(function (th) {
            var idx = Array.prototype.indexOf.call(th.parentNode.cells, th);
            th.classList.add('th-sortable');
            th.addEventListener('click', function () {
                ordenar(idx, th.getAttribute('data-sort'));
            });
        });

        if (paginador) {
            paginador.querySelector('[data-page-prev]').addEventListener('click', function () {
                if (paginaAtual > 1) {
                    paginaAtual--;
                    render();
                }
            });
            paginador.querySelector('[data-page-next]').addEventListener('click', function () {
                paginaAtual++;
                render();
            });
        }

        render();
    });

    // ── Densidade (confortável / compacta) ───────────────────────────────
    var botaoDensidade = document.querySelector('[data-density-toggle]');
    if (botaoDensidade) {
        var alvo = document.querySelector(botaoDensidade.getAttribute('data-density-target') || '');
        var rotulo = botaoDensidade.querySelector('[data-density-label]');

        function aplicarDensidade(compacta) {
            if (alvo) {
                alvo.classList.toggle('table--compact', compacta);
            }
            botaoDensidade.setAttribute('aria-pressed', compacta ? 'true' : 'false');
            if (rotulo) {
                rotulo.textContent = compacta ? 'Confortável' : 'Compactar';
            }
        }

        var compactaSalva = false;
        try {
            compactaSalva = localStorage.getItem('ce-density') === 'compact';
        } catch (e) { /* segue sem persistência */ }
        aplicarDensidade(compactaSalva);

        botaoDensidade.addEventListener('click', function () {
            var compacta = !(alvo && alvo.classList.contains('table--compact'));
            aplicarDensidade(compacta);
            try {
                localStorage.setItem('ce-density', compacta ? 'compact' : 'comfortable');
            } catch (e) { /* idem */ }
        });
    }
})();
