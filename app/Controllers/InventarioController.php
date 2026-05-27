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

        if ($titulo === '') {
            $erros['titulo'] = 'O titulo do inventario e obrigatorio.';
        }

        $categoriasValidas = $this->produtoModel->listarCategorias();
        if ($filtroCategoria !== '' && !in_array($filtroCategoria, $categoriasValidas, true)) {
            $erros['categoria'] = 'A categoria selecionada e invalida ou nao existe.';
        }

        if ($erros === [] && $this->model->contarProdutosAtivos($dados['categoria']) === 0) {
            $erros['produtos'] = 'Nao e possivel abrir um inventario sem produtos ativos cadastrados para o filtro selecionado.';
        }

        if ($erros !== []) {
            $this->inventario_criar($dados, $erros);
            return;
        }

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
            Sessao::setFlashErro('ID do inventario invalido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($id);

        if (!$inventario) {
            Sessao::setFlashErro('Inventario nao encontrado.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        include __DIR__ . '/../Views/inventarios/detalhar.php';
    }

    public function inventario_contagem(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            Sessao::setFlashErro('ID do inventario invalido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($id);
        if (!$inventario) {
            Sessao::setFlashErro('Inventario nao encontrado.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        if (!in_array($inventario['status'], ['aberto', 'em_conferencia'], true)) {
            Sessao::setFlashErro('Este inventario nao esta mais disponivel para contagem.');
            header('Location: index.php?acao=inventario_detalhar&id=' . $id);
            exit;
        }

        $itens = $this->model->listarItens($id);

        include __DIR__ . '/../Views/inventarios/contagem.php';
    }

    public function inventario_salvar_contagem(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sessao::setFlashErro('Metodo invalido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventarioId = (int) ($_POST['inventario_id'] ?? 0);
        $contagens = $_POST['contagens'] ?? [];
        $observacoes = $_POST['observacoes'] ?? [];

        if ($inventarioId <= 0 || empty($contagens)) {
            Sessao::setFlashErro('Dados invalidos.');
            header('Location: index.php?acao=inventario_contagem&id=' . $inventarioId);
            exit;
        }

        $resultado = $this->model->salvarContagens($inventarioId, $contagens, $observacoes);

        if ($resultado) {
            Sessao::setFlashSucesso('Contagens salvas com sucesso!');
            header('Location: index.php?acao=inventario_detalhar&id=' . $inventarioId);
        } else {
            Sessao::setFlashErro('Erro ao salvar as contagens. Verifique os dados.');
            header('Location: index.php?acao=inventario_contagem&id=' . $inventarioId);
        }
        exit;
    }

    public function inventario_divergencias(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            Sessao::setFlashErro('ID do inventario invalido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($id);
        if (!$inventario) {
            Sessao::setFlashErro('Inventario nao encontrado.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $itens = $this->model->listarItens($id);

        include __DIR__ . '/../Views/inventarios/divergencias.php';
    }

    public function inventario_auditoria(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            Sessao::setFlashErro('ID do inventario invalido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($id);
        if (!$inventario) {
            Sessao::setFlashErro('Inventario nao encontrado.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $auditorias = $this->model->listarAuditoria($id);

        include __DIR__ . '/../Views/inventarios/auditoria.php';
    }

    public function inventario_aprovar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?acao=inventarios');
            exit;
        }

        if (!Auth::isAdmin()) {
            Sessao::setFlashErro('Apenas administradores podem aprovar ajustes de inventario.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventarioId = (int) ($_POST['inventario_id'] ?? 0);

        if ($inventarioId <= 0) {
            Sessao::setFlashErro('ID do inventario invalido.');
            header('Location: index.php?acao=inventarios');
            exit;
        }

        $inventario = $this->model->buscarPorId($inventarioId);
        if (!$inventario || $inventario['status'] !== 'em_conferencia') {
            Sessao::setFlashErro('Somente inventarios em conferencia podem ser aprovados.');
            header('Location: index.php?acao=inventario_detalhar&id=' . $inventarioId);
            exit;
        }

        if ($this->model->temContagensPendentes($inventarioId)) {
            Sessao::setFlashErro('Informe a contagem de todos os itens antes de aprovar o inventario.');
            header('Location: index.php?acao=inventario_divergencias&id=' . $inventarioId);
            exit;
        }

        $resultado = $this->model->aprovarInventario($inventarioId, Sessao::getId());

        if ($resultado) {
            Sessao::setFlashSucesso('Inventario aprovado e estoque atualizado com sucesso!');
        } else {
            Sessao::setFlashErro('Nao foi possivel aprovar o inventario.');
        }

        header('Location: index.php?acao=inventario_detalhar&id=' . $inventarioId);
        exit;
    }
}
