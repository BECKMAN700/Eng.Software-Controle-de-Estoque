<?php

require_once __DIR__ . '/../../app/Helpers/Validacao.php';
require_once __DIR__ . '/../../app/Models/InventarioModel.php';

function testeUnitValidacao(TestCase $teste): void
{
    // 1. Produto sem nome
    $prodSemNome = [
        'nome' => '',
        'quantidade' => 10,
        'estoque_minimo' => 2,
        'estoque_maximo' => 20,
        'preco' => 3500.00,
        'status' => 'ativo',
    ];
    $erros = Validacao::produto($prodSemNome);
    $teste->assertArrayHasKey('nome', $erros, 'Validação deve rejeitar produto sem nome.');

    // 2. Quantidade negativa
    $prodQtdNegativa = [
        'nome' => 'Produto Teste',
        'quantidade' => -5,
        'estoque_minimo' => 2,
        'estoque_maximo' => 20,
        'preco' => 10.00,
        'status' => 'ativo',
    ];
    $erros = Validacao::produto($prodQtdNegativa);
    $teste->assertArrayHasKey('quantidade', $erros, 'Validação deve rejeitar quantidade de produto negativa.');

    // 3. Preço inválido (negativo)
    $prodPrecoNegativo = [
        'nome' => 'Produto Teste',
        'quantidade' => 10,
        'estoque_minimo' => 2,
        'estoque_maximo' => 20,
        'preco' => -1.50,
        'status' => 'ativo',
    ];
    $erros = Validacao::produto($prodPrecoNegativo);
    $teste->assertArrayHasKey('preco', $erros, 'Validação deve rejeitar preco negativo.');

    // 4. Estoque mínimo e máximo (max < min)
    $prodEstoqueMinMaxInvalido = [
        'nome' => 'Produto Teste',
        'quantidade' => 10,
        'estoque_minimo' => 15,
        'estoque_maximo' => 5,
        'preco' => 25.00,
        'status' => 'ativo',
    ];
    $erros = Validacao::produto($prodEstoqueMinMaxInvalido);
    $teste->assertArrayHasKey('estoque_maximo', $erros, 'Validação deve rejeitar estoque maximo menor que estoque minimo.');

    // Outros casos válidos de estoque minimo e maximo
    $prodEstoqueValido = [
        'nome' => 'Produto Teste',
        'quantidade' => 10,
        'estoque_minimo' => 5,
        'estoque_maximo' => 15,
        'preco' => 25.00,
        'status' => 'ativo',
    ];
    $teste->assertEquals([], Validacao::produto($prodEstoqueValido), 'Produto com limites de estoque corretos deve passar.');

    // 5. Contagem inválida
    // Texto em vez de número
    $errosTexto = InventarioModel::validarContagem('abc');
    $teste->assertArrayHasKey('quantidade_contada', $errosTexto, 'Contagem nao numerica deve ser rejeitada.');

    // Número negativo
    $errosNegativo = InventarioModel::validarContagem(-3);
    $teste->assertArrayHasKey('quantidade_contada', $errosNegativo, 'Contagem negativa deve ser rejeitada.');

    // Contagem com número decimal
    $errosDecimal = InventarioModel::validarContagem(5.5);
    $teste->assertArrayHasKey('quantidade_contada', $errosDecimal, 'Contagem nao inteira deve ser rejeitada.');

    // Contagem válida
    $teste->assertEquals([], InventarioModel::validarContagem(0), 'Contagem zero deve ser valida.');
    $teste->assertEquals([], InventarioModel::validarContagem(150), 'Contagem inteira positiva deve ser valida.');
    $teste->assertEquals([], InventarioModel::validarContagem('42'), 'Contagem em string numerica inteira deve ser valida.');

    // 6. Testes do período do relatório
    // Datas vazias
    $errosDatasVazias = Validacao::periodoRelatorio(['data_inicial' => '', 'data_final' => '']);
    $teste->assertArrayHasKey('data_inicial', $errosDatasVazias, 'Periodo sem data inicial deve retornar erro.');
    $teste->assertArrayHasKey('data_final', $errosDatasVazias, 'Periodo sem data final deve retornar erro.');

    // Data final menor que inicial
    $errosDatasInvertidas = Validacao::periodoRelatorio(['data_inicial' => '2026-06-10', 'data_final' => '2026-06-05']);
    $teste->assertArrayHasKey('data_final', $errosDatasInvertidas, 'Data final menor que a inicial deve retornar erro.');

    // Datas válidas
    $errosDatasValidas = Validacao::periodoRelatorio(['data_inicial' => '2026-06-01', 'data_final' => '2026-06-10']);
    $teste->assertEquals([], $errosDatasValidas, 'Periodo de datas valido nao deve retornar erros.');
}
