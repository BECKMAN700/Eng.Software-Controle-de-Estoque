
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
    </style>
</head>
<body>
    <h1>Controle de Estoque</h1>

    <p>
        <a href="index.php?acao=criar">Cadastrar novo produto</a>
    </p>

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
... (49 linhas)

message.txt
6 KB
﻿
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
    </style>
</head>
<body>
    <h1>Controle de Estoque</h1>

    <p>
        <a href="index.php?acao=criar">Cadastrar novo produto</a>
    </p>

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
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= $produto['id'] ?></td>
                        <td><?= htmlspecialchars($produto['nome'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['codigo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['categoria'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['unidade'] ?? '') ?></td>
                        <td><?= htmlspecialchars($produto['status'] ?? '') ?></td>
                        <td><?= $produto['quantidade'] ?? 0 ?></td>
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
