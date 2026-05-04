<?php
$pageTitle = 'Login';
$pageSubtitle = 'Acesse o sistema com e-mail e senha cadastrados.';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

$erro = trim($_GET['erro'] ?? '');

ob_start();
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Entrar no sistema</h2>
                <p>Informe suas credenciais para acessar o controle de estoque.</p>
            </div>

            <a href="index.php?acao=listar" class="btn btn-secondary">
                Voltar ao painel
            </a>
        </div>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger">
                <?= esc($erro) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?acao=autenticar" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="admin@controleestoque.local"
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
            </div>

            <div class="card mt-3 summary-card-info">
                <div class="card-header">
                    <div>
                        <h3>Credenciais de teste</h3>
                        <p>
                            Os usuarios admin e estoquista estao documentados em docs/SPRINT2.md.
                            A validacao do login sera implementada na proxima feature de autenticacao.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Entrar
                </button>

                <a href="index.php?acao=listar" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
