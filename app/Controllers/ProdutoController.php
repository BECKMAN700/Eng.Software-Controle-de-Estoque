<?php

require_once __DIR__ . '/../Helpers/ApiResponse.php';
require_once __DIR__ . '/../Helpers/Validacao.php';
require_once __DIR__ . '/../Models/ProdutoModel.php';

class ProdutoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    private function responderJson(bool $erro, string $mensagem, $dados = null, int $statusCode = 200, array $erros = []): void
    {
        if ($erro) {
            ApiResponse::erro($mensagem, $statusCode, $erros);
        }

        ApiResponse::sucesso($mensagem, $dados, $statusCode);
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

    /**
     * Converte um preço informado (number "1234.56" do JS ou "R$ 1.234,56"
     * digitado sem JS) para float. Garante robustez da máscara de moeda.
     */
    private function precoParaFloat($valor): float
    {
        $limpo = preg_replace('/[^0-9,.]/', '', (string) $valor);
        if ($limpo === '' || $limpo === null) {
            return 0.0;
        }

        $temVirgula = strpos($limpo, ',') !== false;
        $temPonto = strpos($limpo, '.') !== false;

        if ($temVirgula && $temPonto) {
            // Formato pt-BR: ponto = milhar, vírgula = decimal
            $limpo = str_replace('.', '', $limpo);
            $limpo = str_replace(',', '.', $limpo);
        } elseif ($temVirgula) {
            $limpo = str_replace(',', '.', $limpo);
        }

        return (float) $limpo;
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
            'preco' => $this->precoParaFloat($origem['preco'] ?? 0),
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
            $dados['preco'] = $this->precoParaFloat($origem['preco']);
        }

        return $dados;
    }

    private function validarDadosProduto(array $dados): array
    {
        return Validacao::produto($dados);
    }

    private function exigirLoginApi(): void
    {
        if (!Sessao::estaLogado()) {
            $this->responderJson(true, 'Faca login para acessar esta API.', null, 401);
        }
    }

    private function exigirAdminApi(): void
    {
        $this->exigirLoginApi();

        if (!Auth::isAdmin()) {
            $this->responderJson(true, 'Voce nao tem permissao para acessar esta funcionalidade.', null, 403);
        }
    }

    private function executarMovimentacaoApi(int $produtoId, string $tipo, string $motivo, int $quantidade, string $observacao): bool
    {
        $tipo = trim($tipo);
        $motivo = trim($motivo);
        $usuarioId = Sessao::getId();

        if ($tipo === 'entrada') {
            if ($motivo === '') {
                return $this->model->movimentar($produtoId, 'entrada', $quantidade, $observacao, $usuarioId);
            }

            return $this->model->registrarEntrada($produtoId, $motivo, $quantidade, $observacao, $usuarioId);
        }

        if ($tipo === 'saida') {
            if ($motivo === '') {
                return $this->model->movimentar($produtoId, 'saida', $quantidade, $observacao, $usuarioId);
            }

            return $this->model->registrarSaida($produtoId, $motivo, $quantidade, $observacao, $usuarioId);
        }

        return false;
    }

    public function listar()
    {
        $busca = trim($_GET['busca'] ?? '');
        $categoria = trim($_GET['categoria'] ?? '');
        $unidade = trim($_GET['unidade'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $dataInicial = trim($_GET['data_inicial'] ?? '');
        $dataFinal = trim($_GET['data_final'] ?? '');
        $erros = [];

        if ($dataInicial !== '' || $dataFinal !== '') {
            $erros = Validacao::periodoRelatorio([
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal
            ]);
        }

        $produtos = empty($erros)
            ? $this->model->listarFiltrados($busca, $categoria, $unidade, $status, $dataInicial, $dataFinal)
            : [];
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

    public function mostrarCriar(array $dados = [], array $erros = []): void
    {
        include __DIR__ . '/../Views/produtos/criar.php';
    }

    public function salvar()
    {
        $dados = $this->dadosProdutoNormalizados($_POST);
        $erros = $this->validarDadosProduto($dados);

        if ($erros !== []) {
            $this->mostrarCriar($dados, $erros);
            return;
        }

        $this->model->criar($dados);
        header('Location: index.php?acao=listar');
        exit;
    }

    public function mostrarEditar(array $dados = [], array $erros = []): void
    {
        $produto = $dados;

        if ($produto === []) {
            $id = $_GET['id'] ?? 0;
            $produto = $this->model->buscarPorId($id);
        }

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
            $produtoAtual = $this->model->buscarPorId($id) ?: [];
            $produto = array_merge($produtoAtual, $dados, ['id' => (int) $id]);
            $this->mostrarEditar($produto, $erros);
            return;
        }

        $this->model->atualizar($id, $dados);
        header('Location: index.php?acao=listar');
        exit;
    }

    public function excluir()
    {
        $id = $_POST['id'] ?? 0;
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

        $sucesso = $this->model->registrarSaida($id, $motivo, $quantidade, $observacao, Sessao::getId());

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

        $sucesso = $this->model->movimentar($id, $tipo, $quantidade, $observacao, Sessao::getId());

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

        $sucesso = $this->model->registrarEntrada($id, $motivo, $quantidade, $observacao, Sessao::getId());

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
                        $this->responderJson(true, 'Produto nao encontrado.', null, 404);
                    }

                    $this->responderJson(false, 'Produto encontrado com sucesso.', $produto);
                }

                $produtos = $this->model->listarFiltrados(
                    trim((string) ($dadosRequisicao['busca'] ?? '')),
                    trim((string) ($dadosRequisicao['categoria'] ?? '')),
                    trim((string) ($dadosRequisicao['unidade'] ?? '')),
                    trim((string) ($dadosRequisicao['status'] ?? ''))
                );

                $this->responderJson(false, 'Produtos listados com sucesso.', $produtos);
            }

            if ($metodo === 'POST') {
                $this->exigirAdminApi();

                $dados = $this->dadosProdutoNormalizados($dadosRequisicao);
                $erros = $this->validarDadosProduto($dados);

                if ($erros !== []) {
                    $this->responderJson(true, 'Dados invalidos.', null, 422, $erros);
                }

                $novoId = $this->model->criar($dados);

                if (!$novoId) {
                    $this->responderJson(true, 'Nao foi possivel criar o produto.', null, 500);
                }

                $produto = $this->model->buscarPorId($novoId);

                $this->responderJson(false, 'Produto criado com sucesso.', $produto, 201);
            }

            if (in_array($metodo, ['PUT', 'PATCH'], true)) {
                $this->exigirAdminApi();

                if ($id <= 0) {
                    $this->responderJson(true, 'Informe o id do produto.', null, 400);
                }

                $produtoAtual = $this->model->buscarPorId($id);

                if (!$produtoAtual) {
                    $this->responderJson(true, 'Produto nao encontrado.', null, 404);
                }

                $dados = $metodo === 'PATCH'
                    ? $this->dadosProdutoParaPatch($produtoAtual, $dadosRequisicao)
                    : $this->dadosProdutoNormalizados($dadosRequisicao);

                $erros = $this->validarDadosProduto($dados);

                if ($erros !== []) {
                    $this->responderJson(true, 'Dados invalidos.', null, 422, $erros);
                }

                $this->model->atualizar($id, $dados);

                $this->responderJson(false, 'Produto atualizado com sucesso.', $this->model->buscarPorId($id));
            }

            if ($metodo === 'DELETE') {
                $this->exigirAdminApi();

                if ($id <= 0) {
                    $this->responderJson(true, 'Informe o id do produto.', null, 400);
                }

                if (!$this->model->buscarPorId($id)) {
                    $this->responderJson(true, 'Produto nao encontrado.', null, 404);
                }

                $this->model->excluir($id);

                $this->responderJson(false, 'Produto removido com sucesso.');
            }

            $this->responderJson(true, 'Metodo nao permitido.', null, 405);
        } catch (Throwable $e) {
            $this->responderJson(true, 'Erro interno ao processar a requisicao.', null, 500);
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
                        $this->responderJson(true, 'Produto nao encontrado.', null, 404);
                    }

                    $this->responderJson(false, 'Movimentacoes listadas com sucesso.', $this->model->listarMovimentacoes($produtoId));
                }

                $limite = isset($dadosRequisicao['limite']) ? (int) $dadosRequisicao['limite'] : 50;

                if ($limite <= 0) {
                    $this->responderJson(true, 'O limite deve ser maior que zero.', null, 422);
                }

                $this->responderJson(false, 'Movimentacoes listadas com sucesso.', $this->model->listarMovimentacoes(null, $limite));
            }

            if ($metodo === 'POST') {
                $dadosMovimentacao = $dadosRequisicao;
                $dadosMovimentacao['produto_id'] = $produtoId;
                $erros = Validacao::movimentacao($dadosMovimentacao);

                if ($erros !== []) {
                    $this->responderJson(true, 'Dados invalidos.', null, 422, $erros);
                }

                if (!$this->model->buscarPorId($produtoId)) {
                    $this->responderJson(true, 'Produto nao encontrado.', null, 404);
                }

                $tipo = trim((string) ($dadosRequisicao['tipo'] ?? ''));
                $motivo = trim((string) ($dadosRequisicao['motivo'] ?? ''));
                $quantidade = (int) ($dadosRequisicao['quantidade'] ?? 0);
                $observacao = trim((string) ($dadosRequisicao['observacao'] ?? ''));

                $sucesso = $this->executarMovimentacaoApi($produtoId, $tipo, $motivo, $quantidade, $observacao);

                if (!$sucesso) {
                    $this->responderJson(true, 'Nao foi possivel registrar a movimentacao.', null, 422);
                }

                $this->responderJson(false, 'Movimentacao registrada com sucesso.', $this->model->buscarPorId($produtoId), 201);
            }

            $this->responderJson(true, 'Metodo nao permitido.', null, 405);
        } catch (Throwable $e) {
            $this->responderJson(true, 'Erro interno ao processar a requisicao.', null, 500);
        }
    }
    public function mostrarDivergencias()
    {
        $divergencias = $this->model->buscarDivergencias();

        include __DIR__ . '/../Views/produtos/divergencias.php';
    }
}
