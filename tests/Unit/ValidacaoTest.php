<?php

require_once __DIR__ . '/../../app/Helpers/Validacao.php';
require_once __DIR__ . '/../../app/Models/InventarioModel.php';

class ValidacaoTest extends \PHPUnit\Framework\TestCase
{
    public function testProdutoComCamposInvalidosDeveRetornarErros(): void
    {
        $semNome = Validacao::produto([
            'nome' => '', 'quantidade' => 10, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'preco' => 3500.00, 'status' => 'ativo',
        ]);
        $this->assertArrayHasKey('nome', $semNome, 'Validacao deve rejeitar produto sem nome.');

        $qtdNegativa = Validacao::produto([
            'nome' => 'Produto Teste', 'quantidade' => -5, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'preco' => 10.00, 'status' => 'ativo',
        ]);
        $this->assertArrayHasKey('quantidade', $qtdNegativa, 'Validacao deve rejeitar quantidade negativa.');

        $precoNegativo = Validacao::produto([
            'nome' => 'Produto Teste', 'quantidade' => 10, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'preco' => -1.50, 'status' => 'ativo',
        ]);
        $this->assertArrayHasKey('preco', $precoNegativo, 'Validacao deve rejeitar preco negativo.');

        $estoqueInvalido = Validacao::produto([
            'nome' => 'Produto Teste', 'quantidade' => 10, 'estoque_minimo' => 15,
            'estoque_maximo' => 5, 'preco' => 25.00, 'status' => 'ativo',
        ]);
        $this->assertArrayHasKey('estoque_maximo', $estoqueInvalido, 'Validacao deve rejeitar estoque maximo menor que o minimo.');
    }

    public function testProdutoValidoNaoDeveRetornarErros(): void
    {
        $produtoValido = [
            'nome' => 'Produto Teste', 'quantidade' => 10, 'estoque_minimo' => 5,
            'estoque_maximo' => 15, 'preco' => 25.00, 'status' => 'ativo',
        ];
        $this->assertEquals([], Validacao::produto($produtoValido), 'Produto com limites de estoque corretos deve passar.');
    }

    public function testContagemInvalidaDeveSerRejeitada(): void
    {
        $this->assertArrayHasKey('quantidade_contada', InventarioModel::validarContagem('abc'), 'Contagem nao numerica deve ser rejeitada.');
        $this->assertArrayHasKey('quantidade_contada', InventarioModel::validarContagem(-3), 'Contagem negativa deve ser rejeitada.');
        $this->assertArrayHasKey('quantidade_contada', InventarioModel::validarContagem(5.5), 'Contagem nao inteira deve ser rejeitada.');
    }

    public function testContagemValidaDeveSerAceita(): void
    {
        $this->assertEquals([], InventarioModel::validarContagem(0), 'Contagem zero deve ser valida.');
        $this->assertEquals([], InventarioModel::validarContagem(150), 'Contagem inteira positiva deve ser valida.');
        $this->assertEquals([], InventarioModel::validarContagem('42'), 'Contagem em string numerica inteira deve ser valida.');
    }

    public function testPeriodoDeRelatorio(): void
    {
        $vazias = Validacao::periodoRelatorio(['data_inicial' => '', 'data_final' => '']);
        $this->assertArrayHasKey('data_inicial', $vazias, 'Periodo sem data inicial deve retornar erro.');
        $this->assertArrayHasKey('data_final', $vazias, 'Periodo sem data final deve retornar erro.');

        $invertidas = Validacao::periodoRelatorio(['data_inicial' => '2026-06-10', 'data_final' => '2026-06-05']);
        $this->assertArrayHasKey('data_final', $invertidas, 'Data final menor que a inicial deve retornar erro.');

        $validas = Validacao::periodoRelatorio(['data_inicial' => '2026-06-01', 'data_final' => '2026-06-10']);
        $this->assertEquals([], $validas, 'Periodo de datas valido nao deve retornar erros.');
    }
}
