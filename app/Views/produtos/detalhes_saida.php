<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Saída</title>
</head>
<body>
    <h1>Detalhes da Saída</h1>

    <p><strong>Produto:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
    <p><strong>Código:</strong> <?= htmlspecialchars($produto['codigo']) ?></p>
    <p><strong>Quantidade atual:</strong> <?= (int) $produto['quantidade'] ?></p>
    <p><strong>Categoria:</strong> <?= htmlspecialchars($produto['categoria'] ?? '') ?></p>
    <p><strong>Unidade:</strong> <?= htmlspecialchars($produto['unidade'] ?? '') ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($produto['status'] ?? '') ?></p>

    <p><a href="index.php?acao=listar">Voltar</a></p>

    <?php if (empty($historicoSaidas)): ?>
        <p>Este produto ainda não possui registros de saída.</p>
    <?php else: ?>
        <?php
        $motivos = [
            'venda' => 'Venda',
            'consumo_interno' => 'Consumo interno',
            'perda' => 'Perda',
            'avaria' => 'Avaria'
        ];
        ?>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Motivo</th>
                    <th>Quantidade</th>
                    <th>Observação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historicoSaidas as $saida): ?>
                    <tr>
                        <td><?= htmlspecialchars($saida['data_hora'] ?? '') ?></td>
                        <td><?= htmlspecialchars($motivos[$saida['motivo'] ?? ''] ?? ($saida['motivo'] ?? '')) ?></td>
                        <td><?= (int) ($saida['quantidade'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($saida['observacao'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>