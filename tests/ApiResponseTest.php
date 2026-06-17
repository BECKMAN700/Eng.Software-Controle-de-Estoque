<?php

require_once __DIR__ . '/../app/Helpers/ApiResponse.php';

class ApiResponseTest extends \PHPUnit\Framework\TestCase
{
    public function testRespostaDeSucesso(): void
    {
        $sucesso = ApiResponse::montar(false, 'Operacao realizada.', ['id' => 1]);

        $this->assertFalse($sucesso['erro'], 'Resposta de sucesso nao deve marcar erro.');
        $this->assertEquals('Operacao realizada.', $sucesso['mensagem'], 'Resposta deve manter a mensagem informada.');
        $this->assertEquals(['id' => 1], $sucesso['dados'], 'Resposta deve manter os dados informados.');
    }

    public function testRespostaDeErroComValidacoes(): void
    {
        $erro = ApiResponse::montar(true, 'Dados invalidos.', null, [
            'erros' => ['nome' => 'Informe o nome.'],
        ]);

        $this->assertTrue($erro['erro'], 'Resposta de erro deve marcar erro.');
        $this->assertEquals(null, $erro['dados'], 'Resposta de erro pode retornar dados nulos.');
        $this->assertArrayHasKey('erros', $erro, 'Resposta de erro com validacoes deve conter a chave erros.');
        $this->assertArrayHasKey('nome', $erro['erros'], 'Resposta deve preservar erros por campo.');
    }
}
