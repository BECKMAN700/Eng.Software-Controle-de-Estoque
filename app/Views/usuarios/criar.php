<?php
$pageTitle = 'Cadastrar usuario';
$pageSubtitle = 'Crie um novo acesso ao sistema de controle de estoque.';
$dados = $dados ?? [];
$erros = $erros ?? [];

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Novo usuario</h2>
                <p>Preencha os dados abaixo para cadastrar um usuario.</p>
            </div>

            <a href="index.php?acao=usuarios" class="btn btn-secondary">
                Voltar para usuarios
            </a>
        </div>

        <?php if (!empty($erros['geral'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc($erros['geral']) ?>
            </div>
        <?php endif; ?>

        <?php if ($erros !== []): ?>
            <div class="alert alert-danger" role="alert">
                Revise os campos destacados antes de salvar.
            </div>
        <?php endif; ?>

        <form action="index.php?acao=usuario_salvar" method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= esc($dados['nome'] ?? '') ?>"
                        required
                    >
                    <?php if (!empty($erros['nome'])): ?>
                        <small class="form-error"><?= esc($erros['nome']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= esc($dados['email'] ?? '') ?>"
                        required
                    >
                    <?php if (!empty($erros['email'])): ?>
                        <small class="form-error"><?= esc($erros['email']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        minlength="6"
                        required
                    >
                    <?php if (!empty($erros['senha'])): ?>
                        <small class="form-error"><?= esc($erros['senha']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="papel">Papel</label>
                    <select id="papel" name="papel" required>
                        <option value="estoquista" <?= (($dados['papel'] ?? 'estoquista') === 'estoquista') ? 'selected' : '' ?>>
                            Estoquista
                        </option>
                        <option value="admin" <?= (($dados['papel'] ?? '') === 'admin') ? 'selected' : '' ?>>
                            Administrador
                        </option>
                    </select>
                    <?php if (!empty($erros['papel'])): ?>
                        <small class="form-error"><?= esc($erros['papel']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="ativo" <?= (($dados['status'] ?? 'ativo') === 'ativo') ? 'selected' : '' ?>>
                            Ativo
                        </option>
                        <option value="inativo" <?= (($dados['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>
                            Inativo
                        </option>
                    </select>
                    <?php if (!empty($erros['status'])): ?>
                        <small class="form-error"><?= esc($erros['status']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Salvar usuario
                </button>

                <a href="index.php?acao=usuarios" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
