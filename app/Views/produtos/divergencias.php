<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Divergências</title>

    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background: #f4f4f4;
        }

        .baixo {
            background: #fff3cd;
        }

        .alto {
            background: #f8d7da;
        }
    </style>
</head>
<body>

<h1>Relatório de Divergências</h1>

<a href="index.php?acao=listar">Voltar</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Mínimo</th>
            <th>Máximo</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($divergencias as $produto): ?>

        <?php
            $classe = '';
            $status = 'Normal';

            if ($produto['quantidade'] < $produto['estoque_minimo']) {
                $classe = 'baixo';
                $status = 'Abaixo do mínimo';
            }

            if (
                $produto['estoque_maximo'] != null &&
                $produto['quantidade'] > $produto['estoque_maximo']
            ) {
                $classe = 'alto';
                $status = 'Acima do máximo';
            }
        ?>

        <tr class="<?= $classe ?>">
            <td><?= $produto['id'] ?></td>
            <td><?= $produto['nome'] ?></td>
            <td><?= $produto['quantidade'] ?></td>
            <td><?= $produto['estoque_minimo'] ?></td>
            <td><?= $produto['estoque_maximo'] ?></td>
            <td><?= $status ?></td>
        </tr>

    <?php endforeach; ?>

    </tbody>
</table>

</body>
</html>