<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Controle de Estoque</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .filtros {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
        }

        .campo-filtro {
            margin-bottom: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        .acoes a {
            margin-right: 8px;
            display: inline-block;
        }

        /* Painel de alerta – estoque acima do máximo */
        .painel-alerta {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .painel-alerta h2 {
            margin-top: 0;
            color: #856404;
        }

        .painel-alerta table {
            background: #fff;
        }

        .painel-alerta thead tr {
            background-color: #ffc107;
        }

        .td-acima {
            font-weight: bold;
            color: #b45309;
        }

        .td-excesso {
            font-weight: bold;
            color: #dc2626;
        }

        /* Cores de alerta na tabela principal */
        .qtd-acima-max {
            background-color: #fef9c3;
            color: #92400e;
            font-weight: bold;
        }

        .qtd-abaixo-min {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Controle de Estoque</h1>

    <p>
        <a href="index.php?acao=criar">Cadastrar novo produto</a>
    </p>

    <!-- ===== PAINEL: PRODUTOS ACIMA DO ESTOQUE MÁXIMO ===== -->
    <?php if (!empty($produtosAcimaMaximo)): ?>
        <div class="painel-alerta">
            <h2>&#9888;&#65039; Produtos acima do estoque máximo (<?= count($produtosAcimaMaximo) ?>)</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Código</th>
                        <th>Categoria</th>
                        <th>Quantidade atual</th>
                        <th>Estoque máximo</th>
                        <th>Excesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtosAcimaMaximo as $p): ?>
                        <?php $excesso = (int)$p['quantidade'] - (int)$p['estoque_maximo']; ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['nome'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['codigo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['categoria'] ?? '') ?></td>
                            <td class="td-acima"><?= $p['quantidade'] ?></td>
                            <td><?= $p['estoque_maximo'] ?></td>
                            <td class="td-excesso">+<?= $excesso ?></td>
                            <td class="acoes">
                                <a href="index.php?acao=editar&id=<?= $p['id'] ?>">Editar</a>
                                <a href="index.php?acao=saida&id=<?= $p['id'] ?>">Registrar saída</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <!-- ===== FIM DO PAINEL ===== -->

    <form class="filtros" action="index.php" method="GET">
        <input type="hidden" name="acao" value="listar">

        <div class="campo-filtro">
            <label>Buscar por nome ou código:</label><br>
            <input type="text" name="busca" value="<?= htmlspecialchars($busca ?? '') ?>">
        </div>

        <div class="campo-filtro">
            <label>Categoria:</label><br>
            <select name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>" <?= (($categoria ?? '') === $item) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-filtro">
            <label>Unidade:</label><br>
            <select name="unidade">
                <option value="">Todas</option>
                <?php foreach ($unidades as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>" <?= (($unidade ?? '') === $item) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-filtro">
            <label>Status:</label><br>
            <select name="status">
                <option value="">Todos</option>
                <?php foreach ($statusOptions as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>" <?= (($status ?? '') === $item) ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($item)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Filtrar</button>
        <a href="index.php?acao=listar">Limpar filtros</a>
    </form>

    <?php
        $temFiltrosAtivos = (
            ($busca ?? '') !== '' ||
            ($categoria ?? '') !== '' ||
            ($unidade ?? '') !== '' ||
            ($status ?? '') !== ''
        );
    ?>

    <?php if (empty($produtos)): ?>
        <?php if ($temFiltrosAtivos): ?>
            <p>Nenhum produto encontrado com os filtros informados.</p>
        <?php else: ?>
            <p>Nenhum produto cadastrado.</p>
        <?php endif; ?>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Categoria</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Quantidade</th>
                    <th>Est. Mín.</th>
                    <th>Est. Máx.</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <?php
                        $qtd = (int)($produto['quantidade'] ?? 0);
                        $max = ($produto['estoque_maximo'] !== null && $produto['estoque_maximo'] !== '') ? (int)$produto['estoque_maximo'] : null;
                        $min = ($produto['estoque_minimo'] !== null && $produto['estoque_minimo'] !== '') ? (int)$produto['estoque_minimo'] : null;
                        $classeQtd = '';
                        if ($max !== null && $qtd > $max) {
                            $classeQtd = 'qtd-acima-max';
                        } elseif ($min !== null && $qtd <= $min) {
                            $classeQtd = 'qtd-abaixo-min';
                        }
                    ?>
                    <tr>
                        <td><?= $produto['id'] ?></td>
                        <td><?= htmlspecialchars($produto['nome'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['codigo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['categoria'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['unidade'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['status'] ?? '') ?></td>
                        <td class="<?= $classeQtd ?>"><?= $qtd ?></td>
                        <td><?= $min !== null ? $min : '—' ?></td>
                        <td><?= $max !== null ? $max : '—' ?></td>
                        <td>R$ <?= number_format((float) ($produto['preco'] ?? 0), 2, ',', '.') ?></td>
                        <td class="acoes">
                            <a href="index.php?acao=editar&id=<?= $produto['id'] ?>">Editar</a>
                            <a href="index.php?acao=excluir&id=<?= $produto['id'] ?>" onclick="return confirm('Deseja excluir este produto?')">Excluir</a>
                            <a href="index.php?acao=movimentar&id=<?= $produto['id'] ?>">Movimentar</a>
                            <a href="index.php?acao=saida&id=<?= $produto['id'] ?>">Registrar saída</a>
                            <a href="index.php?acao=historico_movimentacoes&id=<?= $produto['id'] ?>">Histórico de movimentações</a>
                            <a href="index.php?acao=entrada&id=<?= $produto['id'] ?>">Registrar entrada</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>