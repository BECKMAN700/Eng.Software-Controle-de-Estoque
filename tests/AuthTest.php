<?php

require_once __DIR__ . '/../app/Helpers/Sessao.php';

function testeAuth(TestCase $teste): void
{
    Sessao::iniciar();
    $_SESSION = [];

    $senha = 'admin123';
    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $teste->assertTrue(password_verify($senha, $hash), 'Senha correta deve validar com password_verify.');
    $teste->assertFalse(password_verify('senha_errada', $hash), 'Senha incorreta nao deve validar.');

    Sessao::definirUsuario(1, 'Administrador', 'admin@controleestoque.local', 'admin');

    $teste->assertTrue(Sessao::estaLogado(), 'Sessao deve indicar usuario logado.');
    $teste->assertEquals(1, Sessao::getId(), 'Sessao deve guardar o id do usuario.');
    $teste->assertEquals('Administrador', Sessao::getNome(), 'Sessao deve guardar o nome do usuario.');
    $teste->assertEquals('admin@controleestoque.local', Sessao::getEmail(), 'Sessao deve guardar o e-mail do usuario.');
    $teste->assertEquals('admin', Sessao::getPapel(), 'Sessao deve guardar o papel do usuario.');
    $teste->assertFalse(isset($_SESSION['senha']), 'Sessao nao deve armazenar senha.');
}
