<?php
$pageTitle = 'Abrir inventário';
$dados = $dados ?? [];
$erros = $erros ?? [];
$categorias = $categorias ?? [];
$pageSubtitle = 'Inicie uma nova contagem física do estoque. O sistema salvará o estado atual dos produtos.';

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
                <h2>Iniciar novo inventário</h2>
                <p>Preencha os campos abaixo para dar início ao processo de conferência física.</p>
            </div>

            <a href="index.php?acao=inventarios" class="btn btn-secondary">
                Voltar para lista
            </a>
        </div>

        <?php if ($erros !== []): ?>
            <div class="alert alert-danger" role="alert">
                Revise os campos destacados antes de iniciar o inventário.
                <?php if (!empty($erros['produtos'])): ?>
                    <br><strong><?= esc($erros['produtos']) ?></strong>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?acao=inventario_salvar" method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">

            <div class="form-grid">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="titulo">Título do inventário</label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        placeholder="Ex: Inventário Geral - Maio 2026"
                        value="<?= esc($dados['titulo'] ?? '') ?>"
                        required
                    >
                    <?php if (!empty($erros['titulo'])): ?>
                        <small class="form-error"><?= esc($erros['titulo']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="categoria">Produtos incluídos</label>
                    <select id="categoria" name="categoria">
                        <option value="">Todos os produtos ativos</option>
                        <?php foreach ($categorias as $cat): ?>
                            <?php $selected = (isset($dados['categoria']) && $dados['categoria'] === $cat) ? 'selected' : ''; ?>
                            <option value="<?= esc($cat) ?>" <?= $selected ?>>
                                Apenas da categoria: <?= esc($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($erros['categoria'])): ?>
                        <small class="form-error"><?= esc($erros['categoria']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group mt-2">
                <label for="observacao">Observações / Instruções</label>
                <textarea
                    id="observacao"
                    name="observacao"
                    placeholder="Adicione observações para os conferentes (opcional)."
                ><?= esc($dados['observacao'] ?? '') ?></textarea>
            </div>

            <div class="card mt-3 summary-card-info">
                <div class="card-header">
                    <div>
                        <h3>Importante sobre a abertura do inventário</h3>
                        <p>
                            Ao clicar em <strong>"Abrir inventário"</strong>, o sistema irá congelar a quantidade atual de todos os produtos do filtro selecionado. Essa quantidade será a base para o cálculo de divergências durante a contagem.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Abrir inventário
                </button>

                <a href="index.php?acao=inventarios" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
