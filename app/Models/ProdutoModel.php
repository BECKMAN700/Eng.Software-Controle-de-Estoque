<?php

class ProdutoModel
{
    private $caminhoArquivo;

    public function __construct()
    {
        $this->caminhoArquivo = __DIR__ . '/../../data/produtos.json';

        if (!file_exists($this->caminhoArquivo)) {
            file_put_contents($this->caminhoArquivo, json_encode([]));
        }
    }

    private function lerDados()
    {
        $json = file_get_contents($this->caminhoArquivo);
        $dados = json_decode($json, true);

        return is_array($dados) ? $dados : [];
    }

    private function salvarDados($produtos)
    {
        file_put_contents(
            $this->caminhoArquivo,
            json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function normalizarTexto($valor)
    {
        return strtolower(trim((string) $valor));
    }

    private function contemTexto($texto, $busca)
    {
        return strpos($texto, $busca) !== false;
    }

    public function listar()
    {
        return $this->lerDados();
    }

    public function listarFiltrados($busca = '', $categoria = '', $unidade = '', $status = '')
    {
        $produtos = $this->lerDados();

        $busca = $this->normalizarTexto($busca);
        $categoria = $this->normalizarTexto($categoria);
        $unidade = $this->normalizarTexto($unidade);
        $status = $this->normalizarTexto($status);

        $filtrados = array_filter($produtos, function ($produto) use ($busca, $categoria, $unidade, $status) {
            $nome = $this->normalizarTexto($produto['nome'] ?? '');
            $codigo = $this->normalizarTexto($produto['codigo'] ?? '');
            $produtoCategoria = $this->normalizarTexto($produto['categoria'] ?? '');
            $produtoUnidade = $this->normalizarTexto($produto['unidade'] ?? '');
            $produtoStatus = $this->normalizarTexto($produto['status'] ?? '');

            $passaBusca = true;
            if ($busca !== '') {
                $passaBusca = $this->contemTexto($nome, $busca) || $this->contemTexto($codigo, $busca);
            }

            $passaCategoria = ($categoria === '' || $produtoCategoria === $categoria);
            $passaUnidade = ($unidade === '' || $produtoUnidade === $unidade);
            $passaStatus = ($status === '' || $produtoStatus === $status);

            return $passaBusca && $passaCategoria && $passaUnidade && $passaStatus;
        });

        return array_values($filtrados);
    }

    public function listarCategorias()
    {
        $produtos = $this->lerDados();
        $categorias = [];

        foreach ($produtos as $produto) {
            $categoria = trim($produto['categoria'] ?? '');
            if ($categoria !== '') {
                $categorias[] = $categoria;
            }
        }

        $categorias = array_values(array_unique($categorias));
        natcasesort($categorias);

        return array_values($categorias);
    }

    public function listarUnidades()
    {
        $produtos = $this->lerDados();
        $unidades = [];

        foreach ($produtos as $produto) {
            $unidade = trim($produto['unidade'] ?? '');
            if ($unidade !== '') {
                $unidades[] = $unidade;
            }
        }

        $unidades = array_values(array_unique($unidades));
        natcasesort($unidades);

        return array_values($unidades);
    }

    public function buscarPorId($id)
    {
        $produtos = $this->lerDados();

        foreach ($produtos as $produto) {
            if ($produto['id'] == $id) {
                return $produto;
            }
        }

        return null;
    }

    public function criar($dados)
    {
        $produtos = $this->lerDados();

        $novoId = 1;
        if (!empty($produtos)) {
            $ids = array_column($produtos, 'id');
            $novoId = max($ids) + 1;
        }

        $novoProduto = [
            'id' => $novoId,
            'nome' => $dados['nome'],
            'codigo' => $dados['codigo'],
            'categoria' => $dados['categoria'],
            'unidade' => $dados['unidade'],
            'descricao' => $dados['descricao'],
            'status' => $dados['status'],
            'quantidade' => (int) $dados['quantidade'],
            'preco' => (float) $dados['preco'],
            'historico_movimentacoes' => []
        ];

        $produtos[] = $novoProduto;
        $this->salvarDados($produtos);
    }

    public function atualizar($id, $dados)
    {
        $produtos = $this->lerDados();

        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                $produto['nome'] = $dados['nome'];
                $produto['codigo'] = $dados['codigo'];
                $produto['quantidade'] = (int) $dados['quantidade'];
                $produto['preco'] = (float) $dados['preco'];


                $produto['categoria'] = $dados['categoria'];
                $produto['unidade'] = $dados['unidade'];
                $produto['descricao'] = $dados['descricao'];
                $produto['status'] = $dados['status'];
                $produto['quantidade'] = (int) $dados['quantidade'];
                $produto['preco'] = (float) $dados['preco'];

                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }

                break;
                break;                
                // Mantém campos extras já gravados no JSON, como o histórico de saídas.
                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }
            }
        }

        $this->salvarDados($produtos);
    }

    public function excluir($id)
    {
        $produtos = $this->lerDados();

        $produtos = array_filter($produtos, function ($produto) use ($id) {
            return $produto['id'] != $id;
        });

        $this->salvarDados(array_values($produtos));
    }

    public function movimentar($id, $tipo, $quantidade)
    {
        $produtos = $this->lerDados();

        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                if ($tipo === 'entrada') {
                    $produto['quantidade'] += (int) $quantidade;
                } elseif ($tipo === 'saida') {
                    if ($produto['quantidade'] < (int) $quantidade) {
                        return false;
                    }
                    $produto['quantidade'] -= (int) $quantidade;
                }

                $this->salvarDados($produtos);
                return true;
            }
        }

        return false;
    }

    public function registrarSaida($id, $motivo, $quantidade, $observacao = '')
    {
        // Valida o motivo da saída e grava uma baixa explícita no histórico do produto.
        $motivosValidos = ['venda', 'consumo_interno', 'perda', 'avaria'];
        $quantidade = (int) $quantidade;

        if ($quantidade <= 0 || !in_array($motivo, $motivosValidos, true)) {
            return false;
        }

        $produtos = $this->lerDados();

        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                if ((int) $produto['quantidade'] < $quantidade) {
                    return false;
                }

                $produto['quantidade'] -= $quantidade;

                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }

                $produto['historico_movimentacoes'][] = [
                    'tipo' => 'saida',
                    'motivo' => $motivo,
                    'quantidade' => $quantidade,
                    'observacao' => trim($observacao),
                    'data_hora' => date('Y-m-d H:i:s')
                    'data_hora' => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s')
                ];

                $this->salvarDados($produtos);
                return true;
            }
        }

        return false;
    }
}