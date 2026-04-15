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
        file_put_contents($this->caminhoArquivo, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function listar()
    {
        return $this->lerDados();
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

                // Mantém campos extras já gravados no JSON, como o histórico de saídas.
                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }
                break;
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
                ];

                $this->salvarDados($produtos);
                return true;
            }
        }

        return false;
    }
}