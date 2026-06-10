<?php

require_once __DIR__ . '/../app/Helpers/ApiResponse.php';
require_once __DIR__ . '/../app/Helpers/Validacao.php';

class ProdutoApiTest extends \PHPUnit\Framework\TestCase
{
    public function testEstruturaDaRespostaDeListagem(): void
    {
        $resposta = ApiResponse::montar(false, 'Produtos listados com sucesso.', [['id' => 1]]);

        $this->assertArrayHasKey('erro', $resposta, 'Resposta da API deve conter a chave erro.');
        $this->assertArrayHasKey('mensagem', $resposta, 'Resposta da API deve conter a chave mensagem.');
        $this->assertArrayHasKey('dados', $resposta, 'Resposta da API deve conter a chave dados.');
        $this->assertFalse($resposta['erro'], 'Resposta de sucesso nao deve marcar erro.');
    }

    public function testProdutoValidoNaoRetornaErros(): void
    {
        $produtoValido = [
            'nome' => 'Notebook', 'quantidade' => 10, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'preco' => 3500, 'status' => 'ativo',
        ];

        $this->assertEquals([], Validacao::produto($produtoValido), 'Produto valido nao deve retornar erros.');
    }

    public function testProdutoInvalidoRetornaErrosPorCampo(): void
    {
        $erros = Validacao::produto([
            'nome' => '', 'quantidade' => -1, 'estoque_minimo' => 5,
            'estoque_maximo' => 2, 'preco' => -10, 'status' => 'bloqueado',
        ]);

        $this->assertArrayHasKey('nome', $erros, 'Produto sem nome deve ser rejeitado.');
        $this->assertArrayHasKey('quantidade', $erros, 'Produto com quantidade negativa deve ser rejeitado.');
        $this->assertArrayHasKey('estoque_maximo', $erros, 'Produto com estoque maximo menor que minimo deve ser rejeitado.');
        $this->assertArrayHasKey('preco', $erros, 'Produto com preco negativo deve ser rejeitado.');
        $this->assertArrayHasKey('status', $erros, 'Produto com status invalido deve ser rejeitado.');
    }
}
