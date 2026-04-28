<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cadastro</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="dashboard-page no-sidebar">
    <div class="app-shell">
        <div class="content-area content-area-centered">
            <header class="topbar topbar-simple">
                <div>
                    <p class="eyebrow">Cadastro de produtos</p>
                    <h1>Novo cadastro</h1>
                </div>

                <a class="ghost-action" href="index.php?acao=listar">Voltar para lista</a>
            </header>

            <section class="hero-card hero-card-slim">
                <div>
                    <p class="eyebrow">Operação rápida</p>
                    <h2>Crie produtos com o mesmo visual do painel.</h2>
                    <p>Preencha os dados principais, defina o estoque e salve sem perder a consistência visual da interface.</p>
                </div>
            </section>

            <main class="panel form-panel form-panel-wide">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Novo item</p>
                        <h3>Dados do produto</h3>
                    </div>
                </div>

                <form class="product-form product-form-grid" action="index.php?acao=salvar" method="POST">
                    <label>
                        <span>Nome do produto</span>
                        <input type="text" name="nome" placeholder="Ex: Teclado mecânico" required>
                    </label>

                    <label>
                        <span>Código</span>
                        <input type="text" name="codigo" placeholder="SKU-0001" required>
                    </label>

                    <label>
                        <span>Quantidade</span>
                        <input type="number" name="quantidade" min="0" placeholder="0" required>
                    </label>

                    <label>
                        <span>Estoque mínimo</span>
                        <input type="number" name="estoque_minimo" min="0" placeholder="0" required>
                    </label>

                    <label>
                        <span>Estoque máximo</span>
                        <input type="number" name="estoque_maximo" min="0" placeholder="Opcional">
                    </label>

                    <label>
                        <span>Preço</span>
                        <input type="number" name="preco" step="0.01" min="0" placeholder="0,00" required>
                    </label>

                    <label>
                        <span>Categoria</span>
                        <input type="text" name="categoria" placeholder="Periféricos" required>
                    </label>

                    <label>
                        <span>Unidade</span>
                        <input type="text" name="unidade" placeholder="Un" required>
                    </label>

                    <label class="form-field-wide">
                        <span>Descrição</span>
                        <textarea name="descricao" rows="5" placeholder="Descreva o produto" required></textarea>
                    </label>

                    <label>
                        <span>Status</span>
                        <select name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="descontinuado">Descontinuado</option>
                        </select>
                    </label>

                    <div class="form-actions form-field-wide">
                        <button class="primary-action" type="submit">Salvar</button>
                        <a class="ghost-action" href="index.php?acao=listar">Cancelar</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>