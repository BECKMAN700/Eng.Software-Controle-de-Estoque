/**
 * Estados de interface: toasts (notificações flutuantes) e modal de
 * confirmação. Ativado por:
 *  - [data-flash-toast] (mensagens flash do servidor viram toast)
 *  - window.toast(mensagem, tipo)  → toast client-side
 *  - form/a/button com [data-confirm="mensagem"] → modal de confirmação
 */
(function () {
    'use strict';

    // ─────────────────────────── Toasts ───────────────────────────
    var ICONES = {
        success: '<path d="M20 6 9 17l-5-5"></path>',
        danger: '<circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path>',
        warning: '<path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path>',
        info: '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>'
    };

    function stack() {
        var s = document.getElementById('toast-stack');
        if (!s) {
            s = document.createElement('div');
            s.id = 'toast-stack';
            s.className = 'toast-stack';
            s.setAttribute('aria-live', 'polite');
            document.body.appendChild(s);
        }
        return s;
    }

    function toast(mensagem, tipo, duracao) {
        tipo = ICONES[tipo] ? tipo : 'info';
        duracao = duracao || 4500;

        var el = document.createElement('div');
        el.className = 'toast toast-' + tipo;
        el.setAttribute('role', tipo === 'danger' ? 'alert' : 'status');
        el.innerHTML =
            '<span class="toast-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + ICONES[tipo] + '</svg></span>' +
            '<span class="toast-msg"></span>' +
            '<button type="button" class="toast-close" aria-label="Fechar">&times;</button>';
        el.querySelector('.toast-msg').textContent = mensagem;
        stack().appendChild(el);

        window.requestAnimationFrame(function () { el.classList.add('is-visible'); });

        var timer = window.setTimeout(fechar, duracao);
        function fechar() {
            window.clearTimeout(timer);
            el.classList.remove('is-visible');
            window.setTimeout(function () { el.remove(); }, 260);
        }
        el.querySelector('.toast-close').addEventListener('click', fechar);
        return el;
    }

    window.toast = toast;

    // Converte as mensagens flash do servidor em toasts (degrada para
    // alerta fixo se o JS estiver desligado).
    document.querySelectorAll('[data-flash-toast]').forEach(function (alerta) {
        toast(alerta.textContent.trim(), alerta.getAttribute('data-flash-toast'), 5000);
        alerta.remove();
    });

    // ─────────────────────── Modal de confirmação ───────────────────────
    var modal;
    var msgEl;
    var okBtn;
    var pendente = null;

    function montarModal() {
        if (modal) {
            return;
        }
        modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.hidden = true;
        modal.innerHTML =
            '<div class="confirm-backdrop" data-confirm-cancel></div>' +
            '<div class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-titulo">' +
            '<h2 id="confirm-titulo" class="confirm-titulo">Confirmar ação</h2>' +
            '<p class="confirm-msg"></p>' +
            '<div class="confirm-actions">' +
            '<button type="button" class="btn btn-secondary" data-confirm-cancel>Cancelar</button>' +
            '<button type="button" class="btn btn-danger" data-confirm-ok>Confirmar</button>' +
            '</div></div>';
        document.body.appendChild(modal);

        msgEl = modal.querySelector('.confirm-msg');
        okBtn = modal.querySelector('[data-confirm-ok]');

        modal.addEventListener('click', function (e) {
            if (e.target.closest('[data-confirm-cancel]')) {
                fecharModal();
            }
        });
        okBtn.addEventListener('click', function () {
            var alvo = pendente;
            fecharModal();
            if (alvo) {
                executar(alvo);
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.hidden) {
                fecharModal();
            }
        });
    }

    function abrirModal(mensagem, alvo, rotuloOk) {
        montarModal();
        msgEl.textContent = mensagem;
        okBtn.textContent = rotuloOk || 'Confirmar';
        pendente = alvo;
        modal.hidden = false;
        document.body.classList.add('modal-open');
        window.requestAnimationFrame(function () {
            modal.classList.add('is-open');
            okBtn.focus();
        });
    }

    function fecharModal() {
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        pendente = null;
    }

    function executar(alvo) {
        alvo.__confirmado = true;
        if (alvo.tagName === 'FORM') {
            if (alvo.requestSubmit) {
                alvo.requestSubmit();
            } else {
                alvo.submit();
            }
        } else if (alvo.href) {
            window.location.href = alvo.href;
        } else {
            alvo.click();
        }
    }

    document.addEventListener('submit', function (e) {
        var form = e.target.closest('form[data-confirm]');
        if (form && !form.__confirmado) {
            e.preventDefault();
            abrirModal(form.getAttribute('data-confirm'), form, form.getAttribute('data-confirm-ok'));
        }
    }, true);

    document.addEventListener('click', function (e) {
        var alvo = e.target.closest('a[data-confirm], button[data-confirm]');
        if (alvo && alvo.type !== 'submit' && !alvo.__confirmado) {
            e.preventDefault();
            abrirModal(alvo.getAttribute('data-confirm'), alvo, alvo.getAttribute('data-confirm-ok'));
        }
    }, true);
})();
