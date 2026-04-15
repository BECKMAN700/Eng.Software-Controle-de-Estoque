<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Controle de Estoque</title>
</head>
<body>
    <h1>Controle de Estoque</h1>

    <p>
        <a href="index.php?acao=criar">Cadastrar novo produto</a>
    </p>

    <?php if (empty($produtos)): ?>
        <p>Nenhum produto cadastrado.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= $produto['id'] ?></td>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= htmlspecialchars($produto['codigo']) ?></td>
                        <td><?= $produto['quantidade'] ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($produto['categoria'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['unidade'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['status'] ?? '') ?></td>
                        <td>
                            <a href="index.php?acao=editar&id=<?= $produto['id'] ?>">Editar</a> |
                            <a href="index.php?acao=excluir&id=<?= $produto['id'] ?>" onclick="return confirm('Deseja excluir este produto?')">Excluir</a> |
                            <a href="index.php?acao=movimentar&id=<?= $produto['id'] ?>">Movimentar</a>
                            |
                            <a href="index.php?acao=saida&id=<?= $produto['id'] ?>">Registrar saída</a>
                            |
                            <a href="index.php?acao=detalhes_saida&id=<?= $produto['id'] ?>">Detalhes da saída</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>