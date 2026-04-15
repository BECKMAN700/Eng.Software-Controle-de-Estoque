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
}