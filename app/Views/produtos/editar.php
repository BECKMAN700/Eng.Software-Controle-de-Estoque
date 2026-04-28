<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
</head>
<body>
    <h1>Editar Produto</h1>

    <form action="index.php?acao=atualizar" method="POST">
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">

        <p>
            <label>Nome:</label><br>
            <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </p>

        <p>
            <label>Código:</label><br>
            <input type="text" name="codigo" value="<?= htmlspecialchars($produto['codigo']) ?>" required>
        </p>

        <p>
            <label>Quantidade:</label><br>
            <input type="number" name="quantidade" min="0" value="<?= $produto['quantidade'] ?>" required>
        </p>

        <p>
            <label>Estoque mínimo:</label><br>
            <input type="number" name="estoque_minimo" min="0" value="<?= (int) ($produto['estoque_minimo'] ?? 0) ?>" required>
        </p>

        <p>
            <label>Estoque máximo:</label><br>
            <input type="number" name="estoque_maximo" min="0" value="<?= htmlspecialchars((string) ($produto['estoque_maximo'] ?? '')) ?>">
        </p>

        <p>
            <label>Preço:</label><br>
            <input type="number" name="preco" step="0.01" min="0" value="<?= $produto['preco'] ?>" required>
        </p>

        <p>
            <label>Categoria:</label><br>
            <input type="text" name="categoria" value="<?= htmlspecialchars($produto['categoria'] ?? '') ?>" required>
        </p>

        <p>
            <label>Unidade:</label><br>
            <input type="text" name="unidade" value="<?= htmlspecialchars($produto['unidade'] ?? '') ?>" required>
        </p>

        <p>
            <label>Descrição:</label><br>
            <textarea name="descricao" rows="4" cols="40" required><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
        </p>

        <p>
            <label>Status:</label><br>
            <select name="status" required>
                <option value="ativo" <?= (($produto['status'] ?? '') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= (($produto['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                <option value="descontinuado" <?= (($produto['status'] ?? '') === 'descontinuado') ? 'selected' : '' ?>>Descontinuado</option>
            </select>
        </p>

        <button type="submit">Atualizar</button>
        <a href="index.php?acao=listar">Voltar</a>
    </form>
</body>
</html>