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
<input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>"
required>
</p>
<p>
<label>Código:</label><br>
<input type="text" name="codigo" value="<?= htmlspecialchars($produto['codigo'])
?>" required>
</p>
<p>
<label>Quantidade:</label><br>
<input type="number" name="quantidade" min="0" value="<?=
$produto['quantidade'] ?>" required>
</p>
<p>
<label>Preço:</label><br>
<input type="number" name="preco" step="0.01" min="0" value="<?=
$produto['preco'] ?>" required>
</p>
<button type="submit">Atualizar</button>
<a href="index.php?acao=listar">Voltar</a>
</form>
</body>
</html>