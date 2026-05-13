<?php

require_once __DIR__ . '/../app/Helpers/Sessao.php';
require_once __DIR__ . '/../app/Helpers/Auth.php';

function testePermissao(TestCase $teste): void
{
    Sessao::iniciar();
    $_SESSION = [];

    Sessao::definirUsuario(1, 'Administrador', 'admin@controleestoque.local', 'admin');
    $teste->assertTrue(Auth::isAdmin(), 'Usuario admin deve ser reconhecido como admin.');
    $teste->assertFalse(Auth::isEstoquista(), 'Usuario admin nao deve ser reconhecido como estoquista.');

    Sessao::definirUsuario(2, 'Estoquista', 'estoquista@controleestoque.local', 'estoquista');
    $teste->assertFalse(Auth::isAdmin(), 'Usuario estoquista nao deve ser reconhecido como admin.');
    $teste->assertTrue(Auth::isEstoquista(), 'Usuario estoquista deve ser reconhecido como estoquista.');
}
