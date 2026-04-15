<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Entrada</title>
</head>
<body>
    <h1>Registrar Entrada de Estoque</h1>

    <p><strong>Produto:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
    <p><strong>Quantidade atual:</strong> <?= (int) $produto['quantidade'] ?></p>

    <form action="index.php?acao=entrada" method="POST">
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">

        <p>
            <label>Motivo da entrada:</label><br>
            <select name="motivo" required>
                <option value="compra">Compra</option>
                <option value="devolucao">Devolução</option>
                <option value="transferencia">Transferência</option>
            </select>
        </p>

        <p>
            <label>Observação:</label><br>
            <textarea name="observacao" rows="4" cols="40"></textarea>
        </p>

        <p>
            <label>Quantidade:</label><br>
            <input type="number" name="quantidade" min="1" required>
        </p>

        <button type="submit">Confirmar entrada</button>
        <a href="index.php?acao=listar">Voltar</a>
    </form>
</body>
</html>