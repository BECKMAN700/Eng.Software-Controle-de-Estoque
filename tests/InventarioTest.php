<?php

require_once __DIR__ . '/../app/Models/InventarioModel.php';

function testeInventario(TestCase $teste): void
{
    $teste->assertEquals(5, InventarioModel::calcularDiferenca(15, 10), 'Diferenca positiva deve indicar sobra fisica.');
    $teste->assertEquals(-3, InventarioModel::calcularDiferenca(7, 10), 'Diferenca negativa deve indicar falta fisica.');
    $teste->assertEquals(0, InventarioModel::calcularDiferenca(10, 10), 'Diferenca zero deve indicar estoque conferido.');
    $teste->assertEquals(null, InventarioModel::calcularDiferenca(null, 10), 'Contagem vazia ainda nao deve gerar diferenca.');

    $teste->assertEquals([], InventarioModel::validarContagem(0), 'Contagem zero deve ser valida.');
    $teste->assertEquals([], InventarioModel::validarContagem('12'), 'Contagem inteira em texto deve ser valida.');

    $errosTexto = InventarioModel::validarContagem('abc');
    $teste->assertArrayHasKey('quantidade_contada', $errosTexto, 'Contagem nao numerica deve ser rejeitada.');

    $errosNegativo = InventarioModel::validarContagem(-1);
    $teste->assertArrayHasKey('quantidade_contada', $errosNegativo, 'Contagem negativa deve ser rejeitada.');

    $errosDecimal = InventarioModel::validarContagem(5.5);
    $teste->assertArrayHasKey('quantidade_contada', $errosDecimal, 'Contagem decimal deve ser rejeitada.');
}
