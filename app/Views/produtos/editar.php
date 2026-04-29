<?php
$pageTitle = 'Editar produto';
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

        <form action="index.php?acao=atualizar" method="POST">
            <input type="hidden" name="id" value="<?= (int) ($produto['id'] ?? 0) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do produto</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= esc($produto['nome'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="codigo">Código</label>
                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        value="<?= esc($produto['codigo'] ?? '') ?>"
                        placeholder="Ex: PROD-001"
                    >
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
                    <label for="quantidade">Quantidade atual</label>
                    <input
                        type="number"
                        id="quantidade"
                        name="quantidade"
                        min="0"
                        value="<?= (int) ($produto['quantidade'] ?? 0) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="preco">Preço</label>
                    <input
                        type="number"
                        id="preco"
                        name="preco"
                        min="0"
                        step="0.01"
                        value="<?= esc($produto['preco'] ?? '0.00') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="estoque_minimo">Estoque mínimo</label>
                    <input
                        type="number"
                        id="estoque_minimo"
                        name="estoque_minimo"
                        min="0"
                        value="<?= (int) ($produto['estoque_minimo'] ?? 0) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="estoque_maximo">Estoque máximo</label>
                    <input
                        type="number"
                        id="estoque_maximo"
                        name="estoque_maximo"
                        min="0"
                        value="<?= esc($produto['estoque_maximo'] ?? '') ?>"
                        placeholder="Opcional"
                    >
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <?php
                            $statusAtual = $produto['status'] ?? 'ativo';
                        ?>

                        <option value="ativo" <?= $statusAtual === 'ativo' ? 'selected' : '' ?>>
                            Ativo
                        </option>

                        <option value="inativo" <?= $statusAtual === 'inativo' ? 'selected' : '' ?>>
                            Inativo
                        </option>

                        <option value="descontinuado" <?= $statusAtual === 'descontinuado' ? 'selected' : '' ?>>
                            Descontinuado
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group mt-2">
                <label for="descricao">Descrição</label>
                <textarea
                    id="descricao"
                    name="descricao"
                    placeholder="Adicione uma descrição ou observação sobre o produto."
                ><?= esc($produto['descricao'] ?? '') ?></textarea>
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
                <button type="submit" class="btn btn-primary">
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