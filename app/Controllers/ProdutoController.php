<?php

require_once __DIR__ . '/../Models/ProdutoModel.php';

class ProdutoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    private function responderJson(array $dados, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function lerPayloadJson(): array
    {
        $conteudo = file_get_contents('php://input');

        if ($conteudo === false || trim($conteudo) === '') {
            return [];
        }

        $dados = json_decode($conteudo, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($dados) ? $dados : [];
    }

    private function dadosDaRequisicao(): array
    {
        return array_merge($_GET, $_POST, $this->lerPayloadJson());
    }

    private function dadosProdutoNormalizados(array $origem): array
    {
        $estoqueMinimo = (int) ($origem['estoque_minimo'] ?? 0);
        $estoqueMaximoBruto = trim((string) ($origem['estoque_maximo'] ?? ''));

        return [
            'nome' => trim((string) ($origem['nome'] ?? '')),
            'codigo' => trim((string) ($origem['codigo'] ?? '')),
            'categoria' => trim((string) ($origem['categoria'] ?? '')),
            'unidade' => trim((string) ($origem['unidade'] ?? '')),
            'descricao' => trim((string) ($origem['descricao'] ?? '')),
            'status' => trim((string) ($origem['status'] ?? 'ativo')),
            'quantidade' => (int) ($origem['quantidade'] ?? 0),
            'estoque_minimo' => $estoqueMinimo,
            'estoque_maximo' => $estoqueMaximoBruto === '' ? null : (int) $estoqueMaximoBruto,
            'preco' => (float) ($origem['preco'] ?? 0),
        ];
    }

    private function dadosProdutoParaPatch(array $produtoAtual, array $origem): array
    {
        $dados = $this->dadosProdutoNormalizados($produtoAtual);

        if (array_key_exists('nome', $origem)) {
            $dados['nome'] = trim((string) $origem['nome']);
        }

        if (array_key_exists('codigo', $origem)) {
            $dados['codigo'] = trim((string) $origem['codigo']);
        }

        if (array_key_exists('categoria', $origem)) {
            $dados['categoria'] = trim((string) $origem['categoria']);
        }

        if (array_key_exists('unidade', $origem)) {
            $dados['unidade'] = trim((string) $origem['unidade']);
        }

        if (array_key_exists('descricao', $origem)) {
            $dados['descricao'] = trim((string) $origem['descricao']);
        }

        if (array_key_exists('status', $origem)) {
            $dados['status'] = trim((string) $origem['status']);
        }

        if (array_key_exists('quantidade', $origem)) {
            $dados['quantidade'] = (int) $origem['quantidade'];
        }

        if (array_key_exists('estoque_minimo', $origem)) {
            $dados['estoque_minimo'] = (int) $origem['estoque_minimo'];
        }

        if (array_key_exists('estoque_maximo', $origem)) {
            $estoqueMaximoBruto = trim((string) $origem['estoque_maximo']);
            $dados['estoque_maximo'] = $estoqueMaximoBruto === '' ? null : (int) $estoqueMaximoBruto;
        }

        if (array_key_exists('preco', $origem)) {
            $dados['preco'] = (float) $origem['preco'];
        }

        return $dados;
    }

    private function validarDadosProduto(array $dados): array
    {
        $erros = [];

        if ($dados['nome'] === '') {
            $erros['nome'] = 'O nome do produto é obrigatório.';
        }

        if ($dados['quantidade'] < 0) {
            $erros['quantidade'] = 'A quantidade não pode ser negativa.';
        }

        if ($dados['estoque_minimo'] < 0) {
            $erros['estoque_minimo'] = 'O estoque mínimo não pode ser negativo.';
        }

        if ($dados['estoque_maximo'] !== null && $dados['estoque_maximo'] < $dados['estoque_minimo']) {
            $erros['estoque_maximo'] = 'O estoque máximo deve ser maior ou igual ao estoque mínimo.';
        }

        $statusValidos = ['ativo', 'inativo', 'descontinuado'];

        if (!in_array($dados['status'], $statusValidos, true)) {
            $erros['status'] = 'Status inválido.';
        }

        return $erros;
    }

    private function exigirLoginApi(): void
    {
        if (!Sessao::estaLogado()) {
            $this->responderJson(['erro' => 'Faça login para acessar esta API.'], 401);
        }
    }

    private function exigirAdminApi(): void
    {
        $this->exigirLoginApi();

        if (!Auth::isAdmin()) {
            $this->responderJson(['erro' => 'Você não tem permissão para acessar esta funcionalidade.'], 403);
        }
    }

    private function executarMovimentacaoApi(int $produtoId, string $tipo, string $motivo, int $quantidade, string $observacao): bool
    {
        $tipo = trim($tipo);
        $motivo = trim($motivo);

        if ($tipo === 'entrada') {
            if ($motivo === '') {
                return $this->model->movimentar($produtoId, 'entrada', $quantidade, $observacao);
            }

            return $this->model->registrarEntrada($produtoId, $motivo, $quantidade, $observacao);
        }

        if ($tipo === 'saida') {
            if ($motivo === '') {
                return $this->model->movimentar($produtoId, 'saida', $quantidade, $observacao);
            }

            return $this->model->registrarSaida($produtoId, $motivo, $quantidade, $observacao);
        }

        return false;
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
        $produtosNoMinimo = $this->model->listarNoMinimo();
        $produtosAcimaDoMaximo = $this->model->listarAcimaDoMaximo();

        include __DIR__ . '/../Views/produtos/listar.php';
    }

    public function catalogo()
    {
        $busca = trim($_GET['busca'] ?? '');
        $categoria = trim($_GET['categoria'] ?? '');
        $unidade = trim($_GET['unidade'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $produtos = $this->model->listarFiltrados($busca, $categoria, $unidade, $status);
        $categorias = $this->model->listarCategorias();
        $unidades = $this->model->listarUnidades();
        $statusOptions = ['ativo', 'inativo', 'descontinuado'];

        include __DIR__ . '/../Views/produtos/catalogo.php';
    }

    public function relatorios()
    {
        $produtos = $this->model->listar();
        $produtosAbaixoDoMinimo = $this->model->listarAbaixoDoMinimo();
        $produtosNoMinimo = $this->model->listarNoMinimo();
        $produtosAcimaDoMaximo = $this->model->listarAcimaDoMaximo();
        $ultimasMovimentacoes = $this->model->buscarUltimasMovimentacoes(8);

        include __DIR__ . '/../Views/produtos/relatorios.php';
    }

    public function mostrarCriar()
    {
        include __DIR__ . '/../Views/produtos/criar.php';
    }

    public function salvar()
    {
        $dados = $this->dadosProdutoNormalizados($_POST);
        $erros = $this->validarDadosProduto($dados);

        if ($erros !== []) {
            die(implode(' ', $erros));
        }

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

        $dados = $this->dadosProdutoNormalizados($_POST);
        $erros = $this->validarDadosProduto($dados);

        if ($erros !== []) {
            die(implode(' ', $erros));
        }

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

    public function apiProdutos(): void
    {
        try {
            $this->exigirLoginApi();

            $metodo = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
            $dadosRequisicao = $this->dadosDaRequisicao();
            $id = (int) ($dadosRequisicao['id'] ?? 0);

            if ($metodo === 'GET') {
                if ($id > 0) {
                    $produto = $this->model->buscarPorId($id);

                    if (!$produto) {
                        $this->responderJson(['erro' => 'Produto não encontrado.'], 404);
                    }

                    $this->responderJson(['dados' => $produto]);
                }

                $produtos = $this->model->listarFiltrados(
                    trim((string) ($dadosRequisicao['busca'] ?? '')),
                    trim((string) ($dadosRequisicao['categoria'] ?? '')),
                    trim((string) ($dadosRequisicao['unidade'] ?? '')),
                    trim((string) ($dadosRequisicao['status'] ?? ''))
                );

                $this->responderJson(['dados' => $produtos]);
            }

            if ($metodo === 'POST') {
                $this->exigirAdminApi();

                $dados = $this->dadosProdutoNormalizados($dadosRequisicao);
                $erros = $this->validarDadosProduto($dados);

                if ($erros !== []) {
                    $this->responderJson(['erro' => 'Dados inválidos.', 'erros' => $erros], 422);
                }

                $novoId = $this->model->criar($dados);

                if (!$novoId) {
                    $this->responderJson(['erro' => 'Não foi possível criar o produto.'], 500);
                }

                $produto = $this->model->buscarPorId($novoId);

                $this->responderJson([
                    'mensagem' => 'Produto criado com sucesso.',
                    'dados' => $produto,
                ], 201);
            }

            if (in_array($metodo, ['PUT', 'PATCH'], true)) {
                $this->exigirAdminApi();

                if ($id <= 0) {
                    $this->responderJson(['erro' => 'Informe o id do produto.'], 400);
                }

                $produtoAtual = $this->model->buscarPorId($id);

                if (!$produtoAtual) {
                    $this->responderJson(['erro' => 'Produto não encontrado.'], 404);
                }

                $dados = $metodo === 'PATCH'
                    ? $this->dadosProdutoParaPatch($produtoAtual, $dadosRequisicao)
                    : $this->dadosProdutoNormalizados($dadosRequisicao);

                $erros = $this->validarDadosProduto($dados);

                if ($erros !== []) {
                    $this->responderJson(['erro' => 'Dados inválidos.', 'erros' => $erros], 422);
                }

                $this->model->atualizar($id, $dados);

                $this->responderJson([
                    'mensagem' => 'Produto atualizado com sucesso.',
                    'dados' => $this->model->buscarPorId($id),
                ]);
            }

            if ($metodo === 'DELETE') {
                $this->exigirAdminApi();

                if ($id <= 0) {
                    $this->responderJson(['erro' => 'Informe o id do produto.'], 400);
                }

                if (!$this->model->buscarPorId($id)) {
                    $this->responderJson(['erro' => 'Produto não encontrado.'], 404);
                }

                $this->model->excluir($id);

                $this->responderJson([
                    'mensagem' => 'Produto removido com sucesso.'
                ]);
            }

            $this->responderJson(['erro' => 'Método não permitido.'], 405);
        } catch (Throwable $e) {
            $this->responderJson(['erro' => 'Erro interno ao processar a requisição.'], 500);
        }
    }

    public function apiMovimentacoes(): void
    {
        try {
            $this->exigirLoginApi();

            $metodo = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
            $dadosRequisicao = $this->dadosDaRequisicao();
            $produtoId = (int) ($dadosRequisicao['produto_id'] ?? $dadosRequisicao['id'] ?? 0);

            if ($metodo === 'GET') {
                if ($produtoId > 0) {
                    $produto = $this->model->buscarPorId($produtoId);

                    if (!$produto) {
                        $this->responderJson(['erro' => 'Produto não encontrado.'], 404);
                    }

                    $this->responderJson([
                        'dados' => $this->model->listarMovimentacoes($produtoId)
                    ]);
                }

                $limite = isset($dadosRequisicao['limite']) ? (int) $dadosRequisicao['limite'] : 50;

                if ($limite <= 0) {
                    $this->responderJson(['erro' => 'O limite deve ser maior que zero.'], 422);
                }

                $this->responderJson([
                    'dados' => $this->model->listarMovimentacoes(null, $limite)
                ]);
            }

            if ($metodo === 'POST') {
                if ($produtoId <= 0) {
                    $this->responderJson(['erro' => 'Informe o produto_id.'], 400);
                }

                if (!$this->model->buscarPorId($produtoId)) {
                    $this->responderJson(['erro' => 'Produto não encontrado.'], 404);
                }

                $tipo = trim((string) ($dadosRequisicao['tipo'] ?? ''));
                $motivo = trim((string) ($dadosRequisicao['motivo'] ?? ''));
                $quantidade = (int) ($dadosRequisicao['quantidade'] ?? 0);
                $observacao = trim((string) ($dadosRequisicao['observacao'] ?? ''));

                if ($tipo === '' || $quantidade <= 0) {
                    $this->responderJson(['erro' => 'Tipo e quantidade são obrigatórios.'], 422);
                }

                $sucesso = $this->executarMovimentacaoApi($produtoId, $tipo, $motivo, $quantidade, $observacao);

                if (!$sucesso) {
                    $this->responderJson(['erro' => 'Não foi possível registrar a movimentação.'], 422);
                }

                $this->responderJson([
                    'mensagem' => 'Movimentação registrada com sucesso.',
                    'dados' => $this->model->buscarPorId($produtoId),
                ], 201);
            }

            $this->responderJson(['erro' => 'Método não permitido.'], 405);
        } catch (Throwable $e) {
            $this->responderJson(['erro' => 'Erro interno ao processar a requisição.'], 500);
        }
    }
}