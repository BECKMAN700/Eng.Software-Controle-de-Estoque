<?php

require_once __DIR__ . '/../app/Models/InventarioModel.php';

class InventarioTest extends \PHPUnit\Framework\TestCase
{
    public function testCalcularDiferenca(): void
    {
        $this->assertEquals(5, InventarioModel::calcularDiferenca(15, 10), 'Diferenca positiva deve indicar sobra fisica.');
        $this->assertEquals(-3, InventarioModel::calcularDiferenca(7, 10), 'Diferenca negativa deve indicar falta fisica.');
        $this->assertEquals(0, InventarioModel::calcularDiferenca(10, 10), 'Diferenca zero deve indicar estoque conferido.');
        $this->assertEquals(null, InventarioModel::calcularDiferenca(null, 10), 'Contagem vazia ainda nao deve gerar diferenca.');
    }

    public function testValidarContagem(): void
    {
        $this->assertEquals([], InventarioModel::validarContagem(0), 'Contagem zero deve ser valida.');
        $this->assertEquals([], InventarioModel::validarContagem('12'), 'Contagem inteira em texto deve ser valida.');

        $this->assertArrayHasKey('quantidade_contada', InventarioModel::validarContagem('abc'), 'Contagem nao numerica deve ser rejeitada.');
        $this->assertArrayHasKey('quantidade_contada', InventarioModel::validarContagem(-1), 'Contagem negativa deve ser rejeitada.');
        $this->assertArrayHasKey('quantidade_contada', InventarioModel::validarContagem(5.5), 'Contagem decimal deve ser rejeitada.');
    }
}
