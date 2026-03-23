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
            'preco' => (float) $dados['preco']
        ];

        $produtos[] = $novoProduto;
        $this->salvarDados($produtos);
    }

}