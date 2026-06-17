<?php

require_once __DIR__ . '/../app/Helpers/Validacao.php';

class MovimentacaoApiTest extends \PHPUnit\Framework\TestCase
{
    public function testMovimentacoesValidasNaoRetornamErros(): void
    {
        $entrada = ['produto_id' => 1, 'tipo' => 'entrada', 'motivo' => 'compra', 'quantidade' => 5];
        $saida = ['produto_id' => 1, 'tipo' => 'saida', 'motivo' => 'venda', 'quantidade' => 2];

        $this->assertEquals([], Validacao::movimentacao($entrada), 'Entrada valida nao deve retornar erros.');
        $this->assertEquals([], Validacao::movimentacao($saida), 'Saida valida nao deve retornar erros.');
    }

    public function testMovimentacaoInvalidaRetornaErrosPorCampo(): void
    {
        $erros = Validacao::movimentacao([
            'produto_id' => '', 'tipo' => 'ajuste', 'motivo' => 'motivo_invalido', 'quantidade' => 0,
        ]);

        $this->assertArrayHasKey('produto_id', $erros, 'Movimentacao sem produto_id deve ser rejeitada.');
        $this->assertArrayHasKey('tipo', $erros, 'Movimentacao com tipo invalido deve ser rejeitada.');
        $this->assertArrayHasKey('quantidade', $erros, 'Movimentacao com quantidade zero deve ser rejeitada.');
    }
}
