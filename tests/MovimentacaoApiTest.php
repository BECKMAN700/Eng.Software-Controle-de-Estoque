<?php

require_once __DIR__ . '/../app/Helpers/Validacao.php';

function testeMovimentacaoApi(TestCase $teste): void
{
    $entradaValida = [
        'produto_id' => 1,
        'tipo' => 'entrada',
        'motivo' => 'compra',
        'quantidade' => 5,
    ];

    $saidaValida = [
        'produto_id' => 1,
        'tipo' => 'saida',
        'motivo' => 'venda',
        'quantidade' => 2,
    ];

    $teste->assertEquals([], Validacao::movimentacao($entradaValida), 'Entrada valida nao deve retornar erros.');
    $teste->assertEquals([], Validacao::movimentacao($saidaValida), 'Saida valida nao deve retornar erros.');

    $movimentacaoInvalida = [
        'produto_id' => '',
        'tipo' => 'ajuste',
        'motivo' => 'motivo_invalido',
        'quantidade' => 0,
    ];

    $erros = Validacao::movimentacao($movimentacaoInvalida);
    $teste->assertArrayHasKey('produto_id', $erros, 'Movimentacao sem produto_id deve ser rejeitada.');
    $teste->assertArrayHasKey('tipo', $erros, 'Movimentacao com tipo invalido deve ser rejeitada.');
    $teste->assertArrayHasKey('quantidade', $erros, 'Movimentacao com quantidade zero deve ser rejeitada.');
}
