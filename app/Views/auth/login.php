<?php
$pageTitle = 'Login';
$flashErro = Sessao::getFlashErro();
$flashSucesso = Sessao::getFlashSucesso();
$assetVersion = '20260617-design';

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
                    <input type="email" id="email" name="email" placeholder="email@exemplo.com" autocomplete="email"
                        required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha"
                            autocomplete="current-password" required style="padding-right: 40px; width: 100%;">
                        <button type="button" id="toggle-senha" style="
                            position: absolute;
                            right: 12px;
                            background: none;
                            border: none;
                            cursor: pointer;
                            font-size: 18px;
                            color: #666;
                            padding: 4px 8px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 32px;
                            height: 32px;
                        " title="Mostrar/Ocultar senha">👁️</button>
                    </div>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="lembrar_email" name="lembrar_email" value="1">
                    <label for="lembrar_email" style="margin: 0; font-size: 14px; cursor: pointer;">Lembrar e-mail</label>
                </div>

                <button type="submit" class="btn btn-primary login-submit">
                    Entrar
                </button>
            </form>

            <script>
                // Toggle mostrar/ocultar senha
                document.addEventListener('DOMContentLoaded', function() {
                    const senhaInput = document.getElementById('senha');
                    const toggleBtn = document.getElementById('toggle-senha');

                    toggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (senhaInput.type === 'password') {
                            senhaInput.type = 'text';
                            toggleBtn.textContent = '🙈';
                        } else {
                            senhaInput.type = 'password';
                            toggleBtn.textContent = '👁️';
                        }
                    });
                });

                // Restaurar e-mail do cookie ao carregar a página
                document.addEventListener('DOMContentLoaded', function() {
                    const emailInput = document.getElementById('email');
                    const lembrarCheckbox = document.getElementById('lembrar_email');
                    const emailCookie = getCookie('controle_estoque_email');

                    if (emailCookie) {
                        emailInput.value = emailCookie;
                        lembrarCheckbox.checked = true;
                    }

                    // Salvar ou remover cookie ao submeter o formulário
                    document.querySelector('.login-form').addEventListener('submit', function() {
                        if (lembrarCheckbox.checked && emailInput.value) {
                            setCookie('controle_estoque_email', emailInput.value, 30);
                        } else {
                            deleteCookie('controle_estoque_email');
                        }
                    });

                    // Atualizar checkbox quando desmarcar
                    lembrarCheckbox.addEventListener('change', function() {
                        if (!this.checked) {
                            deleteCookie('controle_estoque_email');
                        }
                    });
                });

                function getCookie(name) {
                    const nameEQ = name + "=";
                    const cookies = document.cookie.split(';');
                    for (let i = 0; i < cookies.length; i++) {
                        const cookie = cookies[i].trim();
                        if (cookie.indexOf(nameEQ) === 0) {
                            return decodeURIComponent(cookie.substring(nameEQ.length));
                        }
                    }
                    return null;
                }

                function setCookie(name, value, days) {
                    const d = new Date();
                    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                    const expires = "expires=" + d.toUTCString();
                    document.cookie = name + "=" + encodeURIComponent(value) + ";" + expires + ";path=/";
                }

                function deleteCookie(name) {
                    setCookie(name, "", -1);
                }
            </script>
            <p style="margin-top:12px; text-align:center;">Ainda não tem conta? <a href="index.php?acao=cadastro">Cadastre-se</a></p>
        </section>
    </main>
</body>

</html>
