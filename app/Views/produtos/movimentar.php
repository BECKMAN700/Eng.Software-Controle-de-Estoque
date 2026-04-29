<?php
$pageTitle = 'Movimentar estoque';
$pageSubtitle = 'Registre uma entrada ou saída manual para ajustar a quantidade do produto.';

$produto = $produto ?? [];

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

ob_start();

$quantidadeAtual = (int) ($produto['quantidade'] ?? 0);
$estoqueMinimo = (int) ($produto['estoque_minimo'] ?? 0);
$estoqueMaximo = $produto['estoque_maximo'] ?? null;
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Movimentar estoque</h2>
                <p>
                    Use esta tela para fazer uma movimentação manual de entrada ou saída.
                </p>
            </div>

            <a href="index.php?acao=listar" class="btn btn-secondary">
                Voltar para o painel
            </a>
        </div>

        <div class="grid grid-3 mb-2">
            <article class="metric-card">
                <p class="metric-label">Produto</p>
                <strong class="product-card-title">
                    <?= esc($produto['nome'] ?? 'Produto não informado') ?>
                </strong>
                <p class="metric-description">
                    Código: <?= esc($produto['codigo'] ?? 'Sem código') ?>
                </p>
            </article>

            <article class="metric-card <?= $quantidadeAtual <= $estoqueMinimo ? 'summary-card-warning' : '' ?>">
                <p class="metric-label">Quantidade atual</p>
                <strong class="metric-value"><?= $quantidadeAtual ?></strong>
                <p class="metric-description">
                    Unidade: <?= esc($produto['unidade'] ?? '-') ?>
                </p>
            </article>

            <article class="metric-card summary-card-info">
                <p class="metric-label">Limites de estoque</p>
                <strong class="product-card-title">
                    Mín. <?= $estoqueMinimo ?> /
                    Máx. <?= ($estoqueMaximo !== null && $estoqueMaximo !== '') ? (int) $estoqueMaximo : '-' ?>
                </strong>
                <p class="metric-description">
                    Verifique os limites antes de movimentar o produto.
                </p>
            </article>
        </div>

        <?php if ($quantidadeAtual <= 0): ?>
            <div class="alert alert-danger">
                Este produto está sem estoque disponível. Você ainda pode registrar uma entrada, mas não deve registrar saída.
            </div>
        <?php elseif ($estoqueMinimo > 0 && $quantidadeAtual <= $estoqueMinimo): ?>
            <div class="alert alert-danger">
                Atenção: este produto está no estoque mínimo ou abaixo dele.
            </div>
        <?php endif; ?>

        <form action="index.php?acao=movimentar" method="POST">
            <input type="hidden" name="id" value="<?= (int) ($produto['id'] ?? 0) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="tipo">Tipo de movimentação</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione o tipo</option>
                        <option value="entrada">Entrada manual</option>
                        <option value="saida">Saída manual</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade</label>
                    <input
                        type="number"
                        id="quantidade"
                        name="quantidade"
                        min="1"
                        placeholder="Ex: 10"
                        required
                    >
                </div>
            </div>

            <div class="form-group mt-2">
                <label for="observacao">Observação</label>
                <textarea
                    id="observacao"
                    name="observacao"
                    placeholder="Ex: Ajuste manual realizado após conferência física do estoque."
                ></textarea>
            </div>

            <div class="card mt-3 summary-card-warning">
                <div class="card-header">
                    <div>
                        <h3>Quando usar movimentação manual?</h3>
                        <p>
                            Use esta opção para ajustes simples de estoque. Para registros mais detalhados,
                            prefira as telas específicas de entrada ou saída, onde é possível informar o motivo
                            como compra, devolução, venda, perda ou avaria.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Confirmar movimentação
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