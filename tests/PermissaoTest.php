<?php

require_once __DIR__ . '/../app/Helpers/Sessao.php';
require_once __DIR__ . '/../app/Helpers/Auth.php';

class PermissaoTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Sessao::iniciar();
        $_SESSION = [];
    }

    public function testAdminEReconhecidoComoAdmin(): void
    {
        Sessao::definirUsuario(1, 'Administrador', 'admin@controleestoque.local', 'admin');

        $this->assertTrue(Auth::isAdmin(), 'Usuario admin deve ser reconhecido como admin.');
        $this->assertFalse(Auth::isEstoquista(), 'Usuario admin nao deve ser reconhecido como estoquista.');
    }

    public function testEstoquistaEReconhecidoComoEstoquista(): void
    {
        Sessao::definirUsuario(2, 'Estoquista', 'estoquista@controleestoque.local', 'estoquista');

        $this->assertFalse(Auth::isAdmin(), 'Usuario estoquista nao deve ser reconhecido como admin.');
        $this->assertTrue(Auth::isEstoquista(), 'Usuario estoquista deve ser reconhecido como estoquista.');
    }
}
