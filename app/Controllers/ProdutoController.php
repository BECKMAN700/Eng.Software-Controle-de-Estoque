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
<?php

class ProdutoModel
{
    private $caminhoArquivo;


<?php

require_once __DIR__ . '/../Models/ProdutoModel.php';

class ProdutoController
{

<?php

require_once __DIR__ . '/../Models/ProdutoModel.php';

class ProdutoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    public function listar()
    {
        $busca = trim($_GET['busca'] ?? '');
        $categoria = trim($_GET['categoria'] ?? '');
        $unidade = trim($_GET['unidade'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $produtos = $this->model->listarFiltrados($busca, $categoria, $unidade, $status);
        $categorias = $this->model->listarCategorias();
        $unidades = $this->model->listarUnidades();
        $statusOptions = ['ativo', 'inativo', 'descontinuado'];

        include __DIR__ . '/../Views/produtos/listar.php';
    }

    public function mostrarCriar()
    {
        include __DIR__ . '/../Views/produtos/criar.php';
    }

    public function salvar()
    {
        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'codigo' => trim($_POST['codigo'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'unidade' => trim($_POST['unidade'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => trim($_POST['status'] ?? 'ativo'),
            'quantidade' => (int) ($_POST['quantidade'] ?? 0),
            'preco' => (float) ($_POST['preco'] ?? 0)
        ];

        $this->model->criar($dados);
        header('Location: index.php?acao=listar');
        exit;
    }

    public function mostrarEditar()
    {
        $id = $_GET['id'] ?? 0;
        $produto = $this->model->buscarPorId($id);

        if (!$produto) {
            echo "Produto não encontrado.";
            return;
        }

        include __DIR__ . '/../Views/produtos/editar.php';
    }

    public function atualizar()
    {
        $id = $_POST['id'] ?? 0;

        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'codigo' => trim($_POST['codigo'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'unidade' => trim($_POST['unidade'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => trim($_POST['status'] ?? 'ativo'),
            'quantidade' => (int) ($_POST['quantidade'] ?? 0),
            'preco' => (float) ($_POST['preco'] ?? 0)
        ];

        $this->model->atualizar($id, $dados);
        header('Location: index.php?acao=listar');
        exit;
    }

    public function excluir()
    {
        $id = $_GET['id'] ?? 0;
        $this->model->excluir($id);

        header('Location: index.php?acao=listar');
        exit;
    }

    public function mostrarMovimentar()
    {
        $id = $_GET['id'] ?? 0;
        $produto = $this->model->buscarPorId($id);

        if (!$produto) {
            echo "Produto não encontrado.";
            return;
        }

        include __DIR__ . '/../Views/produtos/movimentar.php';
    }

    public function mostrarSaida()
    {
        // Exibe uma tela própria para registrar saída de estoque com o motivo da baixa.
        $id = $_GET['id'] ?? 0;
        $produto = $this->model->buscarPorId($id);

        if (!$produto) {
            echo "Produto não encontrado.";
            return;
        }

        include __DIR__ . '/../Views/produtos/saida.php';
    }

    public function mostrarDetalhesSaida()
    {
        $id = $_GET['id'] ?? 0;
        $produto = $this->model->buscarPorId($id);

        if (!$produto) {
            echo "Produto não encontrado.";
            return;
        }

        $historicoSaidas = array_values(array_filter(
            $produto['historico_movimentacoes'] ?? [],
            function ($movimentacao) {
                return ($movimentacao['tipo'] ?? '') === 'saida';
            }
        ));

        include __DIR__ . '/../Views/produtos/detalhes_saida.php';
    }

    public function registrarSaida()
    {
        // Recebe os dados da saída e envia para o model validar e persistir a baixa.
        $id = $_POST['id'] ?? 0;
        $motivo = $_POST['motivo'] ?? '';
        $quantidade = $_POST['quantidade'] ?? 0;
        $observacao = $_POST['observacao'] ?? '';

        $sucesso = $this->model->registrarSaida($id, $motivo, $quantidade, $observacao);

        if (!$sucesso) {
            echo "Não foi possível registrar a saída de estoque.";
            return;
        }

        header('Location: index.php?acao=listar');
        exit;
    }

    public function movimentar()
    {
        $id = $_POST['id'] ?? 0;
        $tipo = $_POST['tipo'] ?? '';
        $quantidade = $_POST['quantidade'] ?? 0;
        $observacao = $_POST['observacao'] ?? '';

        $sucesso = $this->model->movimentar($id, $tipo, $quantidade, $observacao);

        if (!$sucesso) {
            echo "Não foi possível realizar a movimentação.";
            return;
        }

        header('Location: index.php?acao=listar');
        exit;
    }

    public function mostrarHistoricoMovimentacoes()
    {
        $id = $_GET['id'] ?? 0;
        $produto = $this->model->buscarPorId($id);

        if (!$produto) {
            echo "Produto não encontrado.";
            return;
        }

        $historico = $this->model->buscarHistoricoPorProduto($id);

        include __DIR__ . '/../Views/produtos/historico_movimentacoes.php';
    }

    public function mostrarEntrada()
        {
            $id = $_GET['id'] ?? 0;
            $produto = $this->model->buscarPorId($id);

            if (!$produto) {
                echo "Produto não encontrado.";
                return;
            }

            include __DIR__ . '/../Views/produtos/entrada.php';
        }

    public function registrarEntrada()
        {
            $id = $_POST['id'] ?? 0;
            $motivo = $_POST['motivo'] ?? '';
            $quantidade = $_POST['quantidade'] ?? 0;
            $observacao = $_POST['observacao'] ?? '';

            $sucesso = $this->model->registrarEntrada($id, $motivo, $quantidade, $observacao);

            if (!$sucesso) {
                echo "Não foi possível registrar a entrada de estoque.";
                return;
            }

            header('Location: index.php?acao=listar');
            exit;
        }
}