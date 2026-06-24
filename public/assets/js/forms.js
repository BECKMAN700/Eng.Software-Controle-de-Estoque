/**
 * Aprimoramentos de formulário:
 *  - máscaras (moeda R$ 0,00 e código padronizado)
 *  - validação inline com mensagem ao lado do campo + foco no primeiro erro
 *  - botão de salvar com estado de carregando (evita duplo envio)
 * Ativado por: form[data-validate] e campos [data-mask].
 */
(function () {
    'use strict';

    // ───────────────────────── Máscaras ─────────────────────────
    function soDigitos(valor) {
        return (String(valor).match(/\d/g) || []).join('');
    }

    function digitosParaMoeda(digitos) {
        if (!digitos) {
            return 'R$ 0,00';
        }
        digitos = digitos.replace(/^0+(?=\d{3})/, '');
        while (digitos.length < 3) {
            digitos = '0' + digitos;
        }
        var centavos = digitos.slice(-2);
        var inteiro = digitos.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'R$ ' + inteiro + ',' + centavos;
    }

    function numeroParaMoeda(numero) {
        var n = parseFloat(numero);
        if (isNaN(n) || n < 0) {
            n = 0;
        }
        return digitosParaMoeda(String(Math.round(n * 100)));
    }

    function moedaParaNumero(valor) {
        var d = soDigitos(valor);
        if (!d) {
            return '0.00';
        }
        return (parseInt(d, 10) / 100).toFixed(2);
    }

    function aplicarMascaras(escopo) {
        escopo.querySelectorAll('[data-mask="moeda"]').forEach(function (campo) {
            campo.value = campo.value !== '' ? numeroParaMoeda(campo.value) : 'R$ 0,00';
            campo.addEventListener('input', function () {
                campo.value = digitosParaMoeda(soDigitos(campo.value));
            });
        });

        escopo.querySelectorAll('[data-mask="codigo"]').forEach(function (campo) {
            campo.addEventListener('input', function () {
                var pos = campo.selectionStart;
                campo.value = campo.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
                try { campo.setSelectionRange(pos, pos); } catch (e) { /* noop */ }
            });
        });
    }

    // ───────────────────────── Validação ─────────────────────────
    function grupo(campo) {
        return campo.closest('.form-group') || campo.parentNode;
    }

    function definirErro(campo, mensagem) {
        var temErro = !!mensagem;
        campo.classList.toggle('field-invalid', temErro);
        if (temErro) {
            campo.setAttribute('aria-invalid', 'true');
        } else {
            campo.removeAttribute('aria-invalid');
        }

        var alvo = grupo(campo);
        var elemento = alvo.querySelector('.form-error');
        if (temErro) {
            if (!elemento) {
                elemento = document.createElement('small');
                elemento.className = 'form-error';
                alvo.appendChild(elemento);
            }
            elemento.textContent = mensagem;
        } else if (elemento) {
            elemento.textContent = '';
        }
    }

    function validarCampo(campo) {
        if (campo.disabled || campo.type === 'hidden' || campo.name === 'csrf_token') {
            return true;
        }

        var valor = (campo.value || '').trim();

        if (campo.hasAttribute('required') && valor === '') {
            definirErro(campo, 'Campo obrigatório.');
            return false;
        }

        if (campo.type === 'number' && valor !== '') {
            var numero = Number(valor);
            if (isNaN(numero)) {
                definirErro(campo, 'Informe um número válido.');
                return false;
            }
            var min = campo.getAttribute('min');
            if (min !== null && min !== '' && numero < parseFloat(min)) {
                definirErro(campo, 'Não pode ser menor que ' + min + '.');
                return false;
            }
            var max = campo.getAttribute('max');
            if (max !== null && max !== '' && numero > parseFloat(max)) {
                definirErro(campo, 'Não pode ser maior que ' + max + '.');
                return false;
            }
        }

        definirErro(campo, '');
        return true;
    }

    function ativarLoading(form) {
        var botao = form.querySelector('[type="submit"]');
        if (!botao) {
            return;
        }
        var texto = botao.getAttribute('data-loading-text') || 'Salvando…';
        if (botao.tagName === 'BUTTON') {
            botao.dataset.textoOriginal = botao.textContent;
            botao.textContent = texto;
        }
        botao.classList.add('is-loading');
        // adia o disabled para não cancelar o envio em alguns navegadores
        window.setTimeout(function () { botao.disabled = true; }, 0);
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        aplicarMascaras(form);
        var campos = Array.prototype.slice.call(form.querySelectorAll('input, select, textarea'));

        campos.forEach(function (campo) {
            campo.addEventListener('blur', function () { validarCampo(campo); });
            campo.addEventListener('input', function () {
                if (campo.classList.contains('field-invalid')) {
                    validarCampo(campo);
                }
            });
        });

        form.addEventListener('submit', function (evento) {
            var primeiroInvalido = null;
            campos.forEach(function (campo) {
                if (!validarCampo(campo) && !primeiroInvalido) {
                    primeiroInvalido = campo;
                }
            });

            if (primeiroInvalido) {
                evento.preventDefault();
                primeiroInvalido.focus();
                primeiroInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Normaliza moeda (R$ 1.234,56 → 1234.56) antes de enviar
            form.querySelectorAll('[data-mask="moeda"]').forEach(function (campo) {
                campo.value = moedaParaNumero(campo.value);
            });

            ativarLoading(form);
        });
    });

    // Foco no primeiro campo com erro vindo do servidor
    var erroServidor = document.querySelector('.field-invalid');
    if (erroServidor && typeof erroServidor.focus === 'function') {
        erroServidor.focus({ preventScroll: true });
        erroServidor.scrollIntoView({ block: 'center' });
    }
})();
