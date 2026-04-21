<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto</title>
</head>
<body>
    <h1>Cadastrar Produto</h1>

    <form action="index.php?acao=salvar" method="POST">
        <p>
            <label>Nome:</label><br>
            <input type="text" name="nome" required>
        </p>

        <p>
            <label>Código:</label><br>
            <input type="text" name="codigo" required>
        </p>

        <p>
            <label>Quantidade:</label><br>
            <input type="number" name="quantidade" min="0" required>
        </p>

        <p>
            <label>Estoque Máximo (opcional):</label><br>
            <input type="number" name="estoque_maximo" min="0">
        </p>

        <p>
            <label>Preço:</label><br>
            <input type="number" name="preco" step="0.01" min="0" required>
        </p>
        <p>
            <label>Categoria:</label><br>
            <input type="text" name="categoria" required>
        </p>

        <p>
            <label>Unidade:</label><br>
            <input type="text" name="unidade" required>
        </p>

        <p>
            <label>Descrição:</label><br>
            <textarea name="descricao" rows="4" cols="40" required></textarea>
        </p>

        <p>
            <label>Status:</label><br>
            <select name="status" required>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
                <option value="descontinuado">Descontinuado</option>
            </select>
        </p>

        <button type="submit">Salvar</button>
        <a href="index.php?acao=listar">Voltar</a>
    </form>
</body>
</html>