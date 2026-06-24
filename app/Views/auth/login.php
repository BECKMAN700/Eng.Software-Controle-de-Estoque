<?php
require_once __DIR__ . '/../partials/icons.php';

$pageTitle = 'Login';
$flashErro = Sessao::getFlashErro();
$flashSucesso = Sessao::getFlashSucesso();
$assetVersion = '20260623-integracao';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> | Controle de Estoque</title>

    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/base.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?= $assetVersion ?>">
</head>

<body class="login-page">
    <main class="login-shell">
        <section class="login-card" aria-labelledby="login-title">
            <div class="login-brand">
                <div class="brand-mark">
                    <img src="assets/img/logo.svg?v=<?= $assetVersion ?>" alt="Controle de Estoque" width="44" height="44">
                </div>
                <div>
                    <strong>Controle de Estoque</strong>
                    <span>Acesso ao sistema</span>
                </div>
            </div>

            <header class="login-header">
                <h1 id="login-title">Entrar</h1>
                <p>Use seu e-mail e senha cadastrados.</p>
            </header>

            <?php if ($flashSucesso !== ''): ?>
                <div class="alert alert-success" role="alert">
                    <?= esc($flashSucesso) ?>
                </div>
            <?php endif; ?>

            <?php if ($flashErro !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc($flashErro) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?acao=autenticar" method="POST" class="login-form">
                <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="email@exemplo.com" autocomplete="email" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="password-field">
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" id="toggle-senha" aria-label="Mostrar senha" title="Mostrar/ocultar senha">
                            <span class="icon-eye"><?= uiIcon('eye', 'icon') ?></span>
                            <span class="icon-eye-off"><?= uiIcon('eye-off', 'icon') ?></span>
                        </button>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="lembrar_email" name="lembrar_email" value="1">
                    <label for="lembrar_email">Lembrar e-mail</label>
                </div>

                <button type="submit" class="btn btn-primary login-submit">
                    Entrar
                </button>
            </form>

            <p class="auth-alt">Ainda não tem conta? <a href="index.php?acao=cadastro">Cadastre-se</a></p>
        </section>
    </main>

    <script>
        (function () {
            var senha = document.getElementById('senha');
            var toggle = document.getElementById('toggle-senha');

            if (toggle && senha) {
                toggle.addEventListener('click', function () {
                    var mostrar = senha.type === 'password';
                    senha.type = mostrar ? 'text' : 'password';
                    toggle.classList.toggle('is-visible', mostrar);
                    toggle.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
                });
            }

            // Lembrar e-mail (cookie de conveniência, 30 dias)
            var email = document.getElementById('email');
            var lembrar = document.getElementById('lembrar_email');
            var form = document.querySelector('.login-form');
            var CHAVE = 'controle_estoque_email';

            function lerCookie(nome) {
                return document.cookie.split(';').map(function (c) { return c.trim(); })
                    .filter(function (c) { return c.indexOf(nome + '=') === 0; })
                    .map(function (c) { return decodeURIComponent(c.substring(nome.length + 1)); })[0] || null;
            }
            function gravarCookie(nome, valor, dias) {
                var d = new Date();
                d.setTime(d.getTime() + (dias * 864e5));
                document.cookie = nome + '=' + encodeURIComponent(valor) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
            }
            function apagarCookie(nome) { gravarCookie(nome, '', -1); }

            if (email && lembrar) {
                var salvo = lerCookie(CHAVE);
                if (salvo) {
                    email.value = salvo;
                    lembrar.checked = true;
                }
                if (form) {
                    form.addEventListener('submit', function () {
                        if (lembrar.checked && email.value) {
                            gravarCookie(CHAVE, email.value, 30);
                        } else {
                            apagarCookie(CHAVE);
                        }
                    });
                }
                lembrar.addEventListener('change', function () {
                    if (!lembrar.checked) { apagarCookie(CHAVE); }
                });
            }
        }());
    </script>
</body>

</html>
