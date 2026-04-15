<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Movimentações</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Histórico de Movimentações</h1>

    <p><strong>Produto:</strong> <?= htmlspecialchars($produto['nome'] ?? '') ?></p>
    <p><strong>Código:</strong> <?= htmlspecialchars($produto['codigo'] ?? '') ?></p>
    <p><strong>Quantidade atual:</strong> <?= (int) ($produto['quantidade'] ?? 0) ?></p>

    <?php if (empty($historico)): ?>
        <p>Nenhuma movimentação registrada para este produto.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Tipo</th>
                    <th>Motivo</th>
                    <th>Quantidade</th>
                    <th>Observação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historico as $mov): ?>
                    <tr>
                        <td><?= htmlspecialchars($mov['data_hora'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($mov['tipo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($mov['motivo'] ?? '-') ?></td>
                        <td><?= (int) ($mov['quantidade'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($mov['observacao'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p>
        <a href="index.php?acao=listar">Voltar</a>
    </p>
</body>
</html>