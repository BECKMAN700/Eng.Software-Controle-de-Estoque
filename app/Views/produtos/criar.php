<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto</title>
    <style>
        .hint {
            font-size: 0.78rem;
            color: #6b7280;
            margin-top: 3px;
        }
        .hint span {
            font-weight: bold;
            color: #0369a1;
        }
    </style>
</head>
<body>
    <h1>Cadastrar Produto</h1>

    <form action="index.php?acao=salvar" method="POST">
        <p>
            <label>Nome:</label><br>
            <input type="text" name="nome" required>
        </p>

        <p>
            <label>Código:</label><br>
            <input type="text" name="codigo" required>
        </p>

        <p>
            <label>Quantidade:</label><br>
            <input type="number" id="quantidade" name="quantidade" min="0" required>
            <small class="hint">Ao informar a quantidade, os limites de estoque são calculados automaticamente.</small>
        </p>

        <p>
            <label>Preço:</label><br>
            <input type="number" name="preco" step="0.01" min="0" required>
        </p>

        <p>
            <label>Categoria:</label><br>
            <input type="text" name="categoria" required>
        </p>

        <p>
            <label>Unidade:</label><br>
            <input type="text" name="unidade" required>
        </p>

        <p>
            <label>Descrição:</label><br>
            <textarea name="descricao" rows="4" cols="40" required></textarea>
        </p>

        <p>
            <label>Status:</label><br>
            <select name="status" required>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
                <option value="descontinuado">Descontinuado</option>
            </select>
        </p>

        <p>
            <label>Estoque Mínimo:</label><br>
            <input type="number" id="estoque_minimo" name="estoque_minimo" min="0" value="5">
            <small class="hint">Calculado como: quantidade <span>&times; 0,2</span></small>
        </p>

        <p>
            <label>Estoque Máximo:</label><br>
            <input type="number" id="estoque_maximo" name="estoque_maximo" min="0" value="50">
            <small class="hint">Calculado como: quantidade <span>&times; 0,8</span></small>
        </p>

        <button type="submit">Salvar</button>
        <a href="index.php?acao=listar">Voltar</a>
    </form>

    <script>
        const inputQtd = document.getElementById('quantidade');
        const inputMin = document.getElementById('estoque_minimo');
        const inputMax = document.getElementById('estoque_maximo');

        inputQtd.addEventListener('input', function () {
            const qty = parseFloat(this.value);

            if (!isNaN(qty) && qty > 0) {
                inputMin.value = Math.round(qty * 0.2);
                inputMax.value = Math.round(qty * 0.8);
            } else {
                // Sem quantidade: volta para os defaults
                inputMin.value = 5;
                inputMax.value = 50;
            }
        });
    </script>
</body>
</html>