<?php
$pageTitle = 'Registrar saída';
$pageSubtitle = 'Registre vendas, consumo interno, perdas ou avarias no estoque.';

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
                <h2>Registrar saída de estoque</h2>
                <p>Use esta tela para remover quantidade do produto e manter o histórico de movimentações.</p>
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
                    Confira os limites antes de registrar a saída.
                </p>
            </article>
        </div>

        <?php if ($quantidadeAtual <= 0): ?>
            <div class="alert alert-danger">
                Este produto está sem estoque disponível. Não é recomendado registrar uma saída.
            </div>
        <?php elseif ($estoqueMinimo > 0 && $quantidadeAtual <= $estoqueMinimo): ?>
            <div class="alert alert-danger">
                Atenção: este produto já está no estoque mínimo ou abaixo dele.
            </div>
        <?php endif; ?>

        <form action="index.php?acao=saida" method="POST">
            <input type="hidden" name="id" value="<?= (int) ($produto['id'] ?? 0) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="motivo">Motivo da saída</label>
                    <select id="motivo" name="motivo" required>
                        <option value="">Selecione o motivo</option>
                        <option value="venda">Venda</option>
                        <option value="consumo_interno">Consumo interno</option>
                        <option value="perda">Perda</option>
                        <option value="avaria">Avaria</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade retirada</label>
                    <input
                        type="number"
                        id="quantidade"
                        name="quantidade"
                        min="1"
                        max="<?= $quantidadeAtual ?>"
                        placeholder="Ex: 5"
                        required
                    >
                </div>
            </div>

            <div class="form-group mt-2">
                <label for="observacao">Observação</label>
                <textarea
                    id="observacao"
                    name="observacao"
                    placeholder="Ex: Saída registrada por venda no balcão."
                ></textarea>
            </div>

            <div class="card mt-3 summary-card-warning">
                <div class="card-header">
                    <div>
                        <h3>Cuidado ao registrar saída</h3>
                        <p>
                            A quantidade informada será removida do estoque atual do produto.
                            O sistema não deve permitir saída maior que a quantidade disponível.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" <?= $quantidadeAtual <= 0 ? 'disabled' : '' ?>>
                    Registrar saída
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