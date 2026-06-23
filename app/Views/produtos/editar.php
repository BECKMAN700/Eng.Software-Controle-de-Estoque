<?php
$pageTitle = 'Editar produto';
$erros = $erros ?? [];
$pageSubtitle = 'Atualize os dados do produto, limites de estoque e informações de controle.';

$produto = $produto ?? [];

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
                <h2>Editar produto</h2>
                <p>Altere as informações necessárias e salve para atualizar o cadastro.</p>
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

        <form action="index.php?acao=atualizar" method="POST" data-validate novalidate>
            <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">
            <input type="hidden" name="id" value="<?= (int) ($produto['id'] ?? 0) ?>">

            <div class="form-section">
                <h3 class="form-section-title">Dados básicos</h3>
                <p class="form-section-hint">Identificação e classificação do produto.</p>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome do produto</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="<?= !empty($erros['nome']) ? 'field-invalid' : '' ?>"
                            value="<?= esc($produto['nome'] ?? '') ?>"
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
                            data-mask="codigo"
                            value="<?= esc($produto['codigo'] ?? '') ?>"
                            placeholder="Ex: PROD-001"
                        >
                        <small class="form-hint">Letras maiúsculas, números e hífen.</small>
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoria</label>
                        <input
                            type="text"
                            id="categoria"
                            name="categoria"
                            value="<?= esc($produto['categoria'] ?? '') ?>"
                            placeholder="Ex: Alimentos"
                        >
                    </div>

                    <div class="form-group">
                        <label for="unidade">Unidade</label>
                        <input
                            type="text"
                            id="unidade"
                            name="unidade"
                            value="<?= esc($produto['unidade'] ?? '') ?>"
                            placeholder="Ex: kg, un, caixa, pacote"
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="<?= !empty($erros['status']) ? 'field-invalid' : '' ?>" required>
                            <?php $statusAtual = $produto['status'] ?? 'ativo'; ?>
                            <option value="ativo" <?= $statusAtual === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= $statusAtual === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                            <option value="descontinuado" <?= $statusAtual === 'descontinuado' ? 'selected' : '' ?>>Descontinuado</option>
                        </select>
                        <?php if (!empty($erros['status'])): ?>
                            <small class="form-error"><?= esc($erros['status']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="descricao">Descrição</label>
                        <textarea
                            id="descricao"
                            name="descricao"
                            placeholder="Adicione uma descrição ou observação sobre o produto."
                        ><?= esc($produto['descricao'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Estoque</h3>
                <p class="form-section-hint">Quantidade atual e limites de reabastecimento.</p>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="quantidade">Quantidade atual</label>
                        <input
                            type="number"
                            id="quantidade"
                            name="quantidade"
                            class="<?= !empty($erros['quantidade']) ? 'field-invalid' : '' ?>"
                            min="0"
                            value="<?= (int) ($produto['quantidade'] ?? 0) ?>"
                            required
                        >
                        <?php if (!empty($erros['quantidade'])): ?>
                            <small class="form-error"><?= esc($erros['quantidade']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="estoque_minimo">Estoque mínimo</label>
                        <input
                            type="number"
                            id="estoque_minimo"
                            name="estoque_minimo"
                            class="<?= !empty($erros['estoque_minimo']) ? 'field-invalid' : '' ?>"
                            min="0"
                            value="<?= (int) ($produto['estoque_minimo'] ?? 0) ?>"
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
                            class="<?= !empty($erros['estoque_maximo']) ? 'field-invalid' : '' ?>"
                            min="0"
                            value="<?= esc($produto['estoque_maximo'] ?? '') ?>"
                            placeholder="Opcional"
                        >
                        <?php if (!empty($erros['estoque_maximo'])): ?>
                            <small class="form-error"><?= esc($erros['estoque_maximo']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Preço</h3>
                <p class="form-section-hint">Valor unitário usado na valorização do estoque.</p>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="preco">Preço unitário</label>
                        <input
                            type="text"
                            id="preco"
                            name="preco"
                            class="<?= !empty($erros['preco']) ? 'field-invalid' : '' ?>"
                            data-mask="moeda"
                            inputmode="numeric"
                            placeholder="R$ 0,00"
                            value="<?= esc($produto['preco'] ?? '0.00') ?>"
                        >
                        <?php if (!empty($erros['preco'])): ?>
                            <small class="form-error"><?= esc($erros['preco']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mt-3 summary-card-warning">
                <div class="card-header">
                    <div>
                        <h3>Atenção ao alterar a quantidade</h3>
                        <p>
                            Esta tela atualiza diretamente a quantidade atual do produto.
                            Para registrar uma entrada ou saída com histórico, use as opções de movimentação no painel.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" data-loading-text="Salvando…">
                    Salvar alterações
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
