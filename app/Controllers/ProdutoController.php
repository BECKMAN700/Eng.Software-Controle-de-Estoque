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

        $produtosAbaixoDoMinimo = $this->model->listarAbaixoDoMinimo();
        $produtosAcimaDoMaximo = $this->model->listarAcimaDoMaximo();

        include __DIR__ . '/../Views/produtos/listar.php';
    }

    public function mostrarCriar()
    {
        include __DIR__ . '/../Views/produtos/criar.php';
    }

    public function salvar()
    {
        $estoqueMinimo = (int) ($_POST['estoque_minimo'] ?? 0);
        $estoqueMaximoBruto = trim((string) ($_POST['estoque_maximo'] ?? ''));
        $estoqueMaximo = $estoqueMaximoBruto === '' ? null : (int) $estoqueMaximoBruto;

        if ($estoqueMinimo < 0) {
            die('O estoque mínimo não pode ser negativo.');
        }

        if ($estoqueMaximo !== null && $estoqueMaximo < $estoqueMinimo) {
            die('O estoque máximo deve ser maior ou igual ao estoque mínimo.');
        }

        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'codigo' => trim($_POST['codigo'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'unidade' => trim($_POST['unidade'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => trim($_POST['status'] ?? 'ativo'),
            'quantidade' => (int) ($_POST['quantidade'] ?? 0),
            'estoque_minimo' => $estoqueMinimo,
            'estoque_maximo' => $estoqueMaximo,
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

        $estoqueMinimo = (int) ($_POST['estoque_minimo'] ?? 0);
        $estoqueMaximoBruto = trim((string) ($_POST['estoque_maximo'] ?? ''));
        $estoqueMaximo = $estoqueMaximoBruto === '' ? null : (int) $estoqueMaximoBruto;

        if ($estoqueMinimo < 0) {
            die('O estoque mínimo não pode ser negativo.');
        }

        if ($estoqueMaximo !== null && $estoqueMaximo < $estoqueMinimo) {
            die('O estoque máximo deve ser maior ou igual ao estoque mínimo.');
        }

        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'codigo' => trim($_POST['codigo'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'unidade' => trim($_POST['unidade'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => trim($_POST['status'] ?? 'ativo'),
            'quantidade' => (int) ($_POST['quantidade'] ?? 0),
            'estoque_minimo' => $estoqueMinimo,
            'estoque_maximo' => $estoqueMaximo,
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