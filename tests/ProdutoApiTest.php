<?php

require_once __DIR__ . '/../app/Helpers/ApiResponse.php';
require_once __DIR__ . '/../app/Helpers/Validacao.php';

function testeProdutoApi(TestCase $teste): void
{
    $resposta = ApiResponse::montar(false, 'Produtos listados com sucesso.', [['id' => 1]]);

    $teste->assertArrayHasKey('erro', $resposta, 'Resposta da API deve conter a chave erro.');
    $teste->assertArrayHasKey('mensagem', $resposta, 'Resposta da API deve conter a chave mensagem.');
    $teste->assertArrayHasKey('dados', $resposta, 'Resposta da API deve conter a chave dados.');
    $teste->assertFalse($resposta['erro'], 'Resposta de sucesso nao deve marcar erro.');

    $produtoValido = [
        'nome' => 'Notebook',
        'quantidade' => 10,
        'estoque_minimo' => 2,
        'estoque_maximo' => 20,
        'preco' => 3500,
        'status' => 'ativo',
    ];

    $teste->assertEquals([], Validacao::produto($produtoValido), 'Produto valido nao deve retornar erros.');

    $produtoInvalido = [
        'nome' => '',
        'quantidade' => -1,
        'estoque_minimo' => 5,
        'estoque_maximo' => 2,
        'preco' => -10,
        'status' => 'bloqueado',
    ];

    $erros = Validacao::produto($produtoInvalido);
    $teste->assertArrayHasKey('nome', $erros, 'Produto sem nome deve ser rejeitado.');
    $teste->assertArrayHasKey('quantidade', $erros, 'Produto com quantidade negativa deve ser rejeitado.');
    $teste->assertArrayHasKey('estoque_maximo', $erros, 'Produto com estoque maximo menor que minimo deve ser rejeitado.');
    $teste->assertArrayHasKey('preco', $erros, 'Produto com preco negativo deve ser rejeitado.');
    $teste->assertArrayHasKey('status', $erros, 'Produto com status invalido deve ser rejeitado.');
}
