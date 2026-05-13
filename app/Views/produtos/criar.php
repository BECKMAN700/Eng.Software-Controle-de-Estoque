<?php
$pageTitle = 'Cadastrar produto';
$dados = $dados ?? [];
$erros = $erros ?? [];
$pageSubtitle = 'Adicione um novo produto ao estoque com quantidade, limites e informações principais.';

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
                <h2>Novo produto</h2>
                <p>Preencha os dados abaixo para cadastrar um produto no sistema.</p>
            </div>

            <a href="index.php?acao=listar" class="btn btn-secondary">
                Voltar para o painel
            </a>
        </div>

        <?php if ($erros !== []): ?>
            <div class="alert alert-danger" role="alert">
                Revise os campos destacados antes de salvar.
            </div>
        <?php endif; ?>

        <form action="index.php?acao=salvar" method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do produto</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Ex: Arroz tipo 1"
                        value="<?= esc($dados['nome'] ?? '') ?>"
                        required
                    >
                    <?php if (!empty($erros['nome'])): ?>
                        <small class="form-error"><?= esc($erros['nome']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="codigo">Código</label>
                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        placeholder="Ex: PROD-001"
                        value="<?= esc($dados['codigo'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <input
                        type="text"
                        id="categoria"
                        name="categoria"
                        placeholder="Ex: Alimentos"
                        value="<?= esc($dados['categoria'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="unidade">Unidade</label>
                    <input
                        type="text"
                        id="unidade"
                        name="unidade"
                        placeholder="Ex: kg, un, caixa, pacote"
                        value="<?= esc($dados['unidade'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade inicial</label>
                    <input
                        type="number"
                        id="quantidade"
                        name="quantidade"
                        min="0"
                        value="<?= esc($dados['quantidade'] ?? 0) ?>"
                        required
                    >
                    <?php if (!empty($erros['quantidade'])): ?>
                        <small class="form-error"><?= esc($erros['quantidade']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="preco">Preço</label>
                    <input
                        type="number"
                        id="preco"
                        name="preco"
                        min="0"
                        step="0.01"
                        value="<?= esc($dados['preco'] ?? '0.00') ?>"
                        required
                    >
                    <?php if (!empty($erros['preco'])): ?>
                        <small class="form-error"><?= esc($erros['preco']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="estoque_minimo">Estoque mínimo</label>
                    <input
                        type="number"
                        id="estoque_minimo"
                        name="estoque_minimo"
                        min="0"
                        value="<?= esc($dados['estoque_minimo'] ?? 0) ?>"
                        required
                    >
                    <?php if (!empty($erros['estoque_minimo'])): ?>
                        <small class="form-error"><?= esc($erros['estoque_minimo']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="estoque_maximo">Estoque máximo</label>
                    <input
                        type="number"
                        id="estoque_maximo"
                        name="estoque_maximo"
                        min="0"
                        placeholder="Opcional"
                        value="<?= esc($dados['estoque_maximo'] ?? '') ?>"
                    >
                    <?php if (!empty($erros['estoque_maximo'])): ?>
                        <small class="form-error"><?= esc($erros['estoque_maximo']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <?php $statusAtual = $dados['status'] ?? 'ativo'; ?>
                        <option value="ativo" <?= $statusAtual === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                        <option value="inativo" <?= $statusAtual === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                        <option value="descontinuado" <?= $statusAtual === 'descontinuado' ? 'selected' : '' ?>>Descontinuado</option>
                    </select>
                    <?php if (!empty($erros['status'])): ?>
                        <small class="form-error"><?= esc($erros['status']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group mt-2">
                <label for="descricao">Descrição</label>
                <textarea
                    id="descricao"
                    name="descricao"
                    placeholder="Adicione uma descrição ou observação sobre o produto."
                ><?= esc($dados['descricao'] ?? '') ?></textarea>
            </div>

            <div class="card mt-3 summary-card-info">
                <div class="card-header">
                    <div>
                        <h3>Importante sobre estoque mínimo e máximo</h3>
                        <p>
                            O estoque mínimo ajuda o sistema a identificar produtos que precisam de reabastecimento.
                            O estoque máximo é opcional e serve para alertar quando há quantidade acima do limite planejado.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Salvar produto
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
