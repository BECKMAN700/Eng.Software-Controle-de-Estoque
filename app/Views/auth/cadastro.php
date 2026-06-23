<?php
$pageTitle = 'Cadastro';
$assetVersion = '20260617-design';
$flashErro = Sessao::getFlashErro();

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
        <section class="login-card" aria-labelledby="cadastro-title">
            <div class="login-brand">
                <div class="brand-mark">
                    <img src="assets/img/logo.svg?v=<?= $assetVersion ?>" alt="Controle de Estoque" width="44" height="44">
                </div>
                <div>
                    <strong>Controle de Estoque</strong>
                    <span>Cadastro de usuário</span>
                </div>
            </div>

            <header class="login-header">
                <h1 id="cadastro-title">Criar conta</h1>
                <p>Preencha os dados para criar sua conta.</p>
            </header>

            <?php if ($flashErro !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc($flashErro) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?acao=cadastro_salvar" method="POST" class="login-form">
                <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">

                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Seu nome" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="email@exemplo.com" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>

                <div class="form-group">
                    <label for="senha_conf">Confirmar senha</label>
                    <input type="password" id="senha_conf" name="senha_conf" placeholder="Repita a senha" required>
                </div>

                <button type="submit" class="btn btn-primary login-submit">Cadastrar</button>
            </form>

            <p style="margin-top:12px; text-align:center;">Já tem conta? <a href="index.php?acao=login">Voltar ao login</a></p>
        </section>
    </main>
</body>

</html>
