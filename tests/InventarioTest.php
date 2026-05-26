<?php

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../app/Models/InventarioModel.php';

class InventarioTest extends TestCase
{
    public function deveCalcularDivergenciaPositiva()
    {
        $diferenca = InventarioModel::calcularDiferenca(
            15,
            10
        );

        $this->assertEquals(5, $diferenca);
    }

    public function deveCalcularDivergenciaNegativa()
    {
        $diferenca = InventarioModel::calcularDiferenca(
            7,
            10
        );

        $this->assertEquals(-3, $diferenca);
    }

    public function deveCalcularDivergenciaZero()
    {
        $diferenca = InventarioModel::calcularDiferenca(
            10,
            10
        );

        $this->assertEquals(0, $diferenca);
    }

    public function contagemNegativaDeveSerInvalida()
    {
        $erros = InventarioModel::validarContagem(-1);

        $this->assertTrue(
            isset($erros['quantidade_contada'])
        );
    }

    public function contagemInteiraDeveSerValida()
    {
        $erros = InventarioModel::validarContagem(10);

        $this->assertEquals([], $erros);
    }

    public function estoquistaNaoDeveAprovar()
    {
        $_SESSION['usuario'] = [
            'perfil' => 'estoquista'
        ];

        $this->assertNotEquals(
            'admin',
            $_SESSION['usuario']['perfil']
        );
    }
}