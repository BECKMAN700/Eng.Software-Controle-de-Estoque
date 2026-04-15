<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Saída de Estoque</title>
</head>
<body>
    <h1>Registrar Saída de Estoque</h1>

    <p><strong>Produto:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
    <p><strong>Código:</strong> <?= htmlspecialchars($produto['codigo']) ?></p>
    <p><strong>Quantidade atual:</strong> <?= (int) $produto['quantidade'] ?></p>

    <form action="index.php?acao=saida" method="POST">
        <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">

        <?php
        // Esta lista deixa claro o motivo da baixa e ajuda a diferenciar venda, consumo interno e perdas.
        $motivos = [
            'venda' => 'Venda',
            'consumo_interno' => 'Consumo interno',
            'perda' => 'Perda',
            'avaria' => 'Avaria'
        ];
        ?>

        <p>
            <label for="motivo">Motivo da saída:</label><br>
            <select name="motivo" id="motivo" required>
                <option value="">Selecione</option>
                <?php foreach ($motivos as $valor => $rotulo): ?>
                    <option value="<?= $valor ?>"><?= $rotulo ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="quantidade">Quantidade:</label><br>
            <input type="number" name="quantidade" id="quantidade" min="1" max="<?= (int) $produto['quantidade'] ?>" required>
        </p>

        <p>
            <label for="observacao">Observação:</label><br>
            <textarea name="observacao" id="observacao" rows="4" cols="40" placeholder="Ex.: produto vencido, peça quebrada, saída para uso da equipe"></textarea>
        </p>

        <button type="submit">Registrar saída</button>
        <a href="index.php?acao=listar">Voltar</a>
    </form>
</body>
</html>