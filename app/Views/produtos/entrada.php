<?php
$pageTitle = 'Registrar entrada';
$pageSubtitle = 'Registre compras, devoluções ou transferências recebidas no estoque.';

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
                <h2>Registrar entrada de estoque</h2>
                <p>Use esta tela para adicionar quantidade ao produto e manter o histórico de movimentações.</p>
            </div>

            <a href="index.php?acao=listar" class="btn btn-secondary">
                Voltar para o painel
            </a>
        </div>

        <div class="grid grid-3 mb-2">
            <article class="metric-card">
                <p class="metric-label">Produto</p>
                <strong class="product-card-title"><?= esc($produto['nome'] ?? 'Produto não informado') ?></strong>
                <p class="metric-description">
                    Código: <?= esc($produto['codigo'] ?? 'Sem código') ?>
                </p>
            </article>

            <article class="metric-card">
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
                    Use esses valores como referência antes de registrar a entrada.
                </p>
            </article>
        </div>

        <form action="index.php?acao=entrada" method="POST">
            <input type="hidden" name="id" value="<?= (int) ($produto['id'] ?? 0) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="motivo">Motivo da entrada</label>
                    <select id="motivo" name="motivo" required>
                        <option value="">Selecione o motivo</option>
                        <option value="compra">Compra</option>
                        <option value="devolucao">Devolução</option>
                        <option value="transferencia">Transferência</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade recebida</label>
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
                    placeholder="Ex: Compra realizada para reposição do estoque."
                ></textarea>
            </div>

            <div class="card mt-3 summary-card-info">
                <div class="card-header">
                    <div>
                        <h3>O que acontece ao confirmar?</h3>
                        <p>
                            A quantidade informada será somada ao estoque atual do produto.
                            Além disso, uma movimentação do tipo entrada será registrada no histórico.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Confirmar entrada
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