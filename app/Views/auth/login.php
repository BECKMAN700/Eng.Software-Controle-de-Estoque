<?php
$pageTitle = 'Login';
$erro = trim($_GET['erro'] ?? '');
$assetVersion = '20260504-menu';

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

    <link rel="stylesheet" href="assets/css/base.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?= $assetVersion ?>">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?= $assetVersion ?>">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-card" aria-labelledby="login-title">
            <div class="login-brand">
                <div class="brand-mark">CE</div>
                <div>
                    <strong>Controle de Estoque</strong>
                    <span>Acesso ao sistema</span>
                </div>
            </div>

            <header class="login-header">
                <h1 id="login-title">Entrar</h1>
                <p>Use seu e-mail e senha cadastrados.</p>
            </header>

            <?php if ($erro !== ''): ?>
                <div class="alert alert-danger">
                    <?= esc($erro) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?acao=autenticar" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="email@exemplo.com"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary login-submit">
                    Entrar
                </button>
            </form>
        </section>
    </main>
</body>
</html>
