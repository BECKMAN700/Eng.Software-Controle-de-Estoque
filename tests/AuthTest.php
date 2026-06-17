<?php

require_once __DIR__ . '/../app/Helpers/Sessao.php';

class AuthTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Sessao::iniciar();
        $_SESSION = [];
    }

    public function testSenhaComPasswordHashEVerify(): void
    {
        $senha = 'admin123';
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $this->assertTrue(password_verify($senha, $hash), 'Senha correta deve validar com password_verify.');
        $this->assertFalse(password_verify('senha_errada', $hash), 'Senha incorreta nao deve validar.');
    }

    public function testSessaoGuardaDadosDoUsuario(): void
    {
        Sessao::definirUsuario(1, 'Administrador', 'admin@controleestoque.local', 'admin');

        $this->assertTrue(Sessao::estaLogado(), 'Sessao deve indicar usuario logado.');
        $this->assertEquals(1, Sessao::getId(), 'Sessao deve guardar o id do usuario.');
        $this->assertEquals('Administrador', Sessao::getNome(), 'Sessao deve guardar o nome do usuario.');
        $this->assertEquals('admin@controleestoque.local', Sessao::getEmail(), 'Sessao deve guardar o e-mail do usuario.');
        $this->assertEquals('admin', Sessao::getPapel(), 'Sessao deve guardar o papel do usuario.');
        $this->assertFalse(isset($_SESSION['senha']), 'Sessao nao deve armazenar senha.');
    }

    public function testTokenCsrf(): void
    {
        $csrfToken = Sessao::getCsrfToken();

        $this->assertNotSame('', $csrfToken, 'Sessao deve gerar token CSRF.');
        $this->assertTrue(Sessao::validarCsrfToken($csrfToken), 'Token CSRF valido deve ser aceito.');
        $this->assertFalse(Sessao::validarCsrfToken('token_invalido'), 'Token CSRF invalido deve ser rejeitado.');
    }
}
