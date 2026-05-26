<?php

require_once __DIR__ . '/../Models/InventarioModel.php';
require_once __DIR__ . '/../Models/ProdutoModel.php';
require_once __DIR__ . '/../Helpers/Sessao.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class InventarioController
{
    private $model;
    private $produtoModel;

    public function __construct()
    {
        $this->model = new InventarioModel();
        $this->produtoModel = new ProdutoModel();
    }

    public function inventarios(): void
    {
        $inventarios = $this->model->listar();
        include __DIR__ . '/../Views/inventarios/listar.php';
    }

    public function inventario_criar(array $dados = [], array $erros = []): void
    {
        $categorias = $this->produtoModel->listarCategorias();
        include __DIR__ . '/../Views/inventarios/criar.php';
    }

    public function inventario_salvar(): void
    {
        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $observacao = trim((string) ($_POST['observacao'] ?? ''));
        $filtroCategoria = trim((string) ($_POST['categoria'] ?? ''));

        $dados = [
            'titulo' => $titulo,
            'observacao' => $observacao,
            'categoria' => $filtroCategoria !== '' ? $filtroCategoria : null,
        ];

        $erros = [];

        // 1. Impedir dados obrigatórios vazios
        if ($titulo === '') {
            $erros['titulo'] = 'O titulo do inventario e obrigatorio.';
        }

        // 2. Impedir categorias inválidas
        $categoriasValidas = $this->produtoModel->listarCategorias();
        if ($filtroCategoria !== '' && !in_array($filtroCategoria, $categoriasValidas, true)) {
            $erros['categoria'] = 'A categoria selecionada e invalida ou nao existe.';
        }

        // 3. Impedir inventário sem produtos
        if ($erros === []) {
            $categoriaFiltro = $dados['categoria'];
            
            // Replicamos a lógica de buscarProdutosParaInventario de forma preventiva
            // para enviar uma mensagem amigável antes de tentar salvar
            $db = new Database();
            $conn = $db->conectar();
            $sql = "SELECT COUNT(*) FROM produtos WHERE status = 'ativo'";
            $params = [];
            if ($categoriaFiltro !== null) {
                $sql .= " AND categoria = :categoria";
                $params[':categoria'] = $categoriaFiltro;
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $totalProdutos = (int) $stmt->fetchColumn();

            if ($totalProdutos === 0) {
                $erros['produtos'] = 'Nao e possivel abrir um inventario sem produtos ativos cadastrados para o filtro selecionado.';
            }
        }

        if ($erros !== []) {
            $this->inventario_criar($dados, $erros);
            return;
        }

        // Set the logged-in user id
        $dados['criado_por'] = Sessao::getId();

        try {
            $inventarioId = $this->model->abrir($dados);

            if ($inventarioId) {
                Sessao::setFlashSucesso('Inventario aberto com sucesso!');
                header('Location: index.php?acao=inventarios');
                exit;
            }

            Sessao::setFlashErro('Nao foi possivel abrir o inventario.');
            $this->inventario_criar($dados, $erros);
        } catch (Exception $e) {
            Sessao::setFlashErro('Erro interno ao abrir o inventario: ' . $e->getMessage());
            $this->inventario_criar($dados, $erros);
        }
    }

    public function inventario_detalhar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            echo "ID do inventário inválido.";
            return;
        }

        $inventario = $this->model->buscarPorId($id);

        if (!$inventario) {
            echo "Inventário não encontrado.";
            return;
        }
        
        include __DIR__ . '/../Views/inventarios/detalhar.php';
    }
        // ====================== CONTAGEM ======================
    public function inventario_contagem(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            Sessao::setFlashErro('ID do inventário inválido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($id);
        if (!$inventario) {
            Sessao::setFlashErro('Inventário não encontrado.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        // Só permite contagem se o inventário estiver aberto ou em conferência
        if (!in_array($inventario['status'], ['aberto', 'em_conferencia'])) {
            Sessao::setFlashErro('Este inventário não está mais disponível para contagem.');
            header('Location: index.php?acao=inventario_detalhar&id=' . $id);
            exit;
        }

        $itens = $this->model->listarItens($id);

        include __DIR__ . '/../Views/inventarios/contagem.php';
    }

    public function inventario_salvar_contagem(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sessao::setFlashErro('Método inválido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventarioId = (int) ($_POST['inventario_id'] ?? 0);
        $contagens = $_POST['contagens'] ?? [];   // nome do campo no formulário

        if ($inventarioId <= 0 || empty($contagens)) {
            Sessao::setFlashErro('Dados inválidos.');
            header('Location: index.php?acao=inventario_contagem&id=' . $inventarioId);
            exit;
        }

        $resultado = $this->model->salvarContagens($inventarioId, $contagens);

        if ($resultado) {
            Sessao::setFlashSucesso('Contagens salvas com sucesso!');
            header('Location: index.php?acao=inventario_detalhar&id=' . $inventarioId);
        } else {
            Sessao::setFlashErro('Erro ao salvar as contagens. Verifique os dados.');
            header('Location: index.php?acao=inventario_contagem&id=' . $inventarioId);
        }
        exit;
    }

    // ====================== DIVERGÊNCIAS ======================
    public function inventario_divergencias(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            Sessao::setFlashErro('ID do inventário inválido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($id);
        if (!$inventario) {
            Sessao::setFlashErro('Inventário não encontrado.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $itens = $this->model->listarItens($id);

        include __DIR__ . '/../Views/inventarios/divergencias.php';
    }

    // ====================== APROVAÇÃO (RESTRITA A ADMIN) ======================
    public function inventario_aprovar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?acao=inventarios');
            exit;
        }

        if (!Auth::isAdmin()) {
            Sessao::setFlashErro('Apenas administradores podem aprovar ajustes de inventário.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventarioId = (int) ($_POST['inventario_id'] ?? 0);

        if ($inventarioId <= 0) {
            Sessao::setFlashErro('ID do inventário inválido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $resultado = $this->model->aprovarInventario($inventarioId, Sessao::getId());

        if ($resultado) {
            Sessao::setFlashSucesso('Inventário aprovado e estoque atualizado com sucesso!');
        } else {
            Sessao::setFlashErro('Não foi possível aprovar o inventário.');
        }

        header('Location: index.php?acao=inventario_detalhar&id=' . $inventarioId);
        exit;
    }
}
