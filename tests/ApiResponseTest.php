<?php

require_once __DIR__ . '/../app/Helpers/ApiResponse.php';

function testeApiResponse(TestCase $teste): void
{
    $sucesso = ApiResponse::montar(false, 'Operacao realizada.', ['id' => 1]);

    $teste->assertFalse($sucesso['erro'], 'Resposta de sucesso nao deve marcar erro.');
    $teste->assertEquals('Operacao realizada.', $sucesso['mensagem'], 'Resposta deve manter a mensagem informada.');
    $teste->assertEquals(['id' => 1], $sucesso['dados'], 'Resposta deve manter os dados informados.');

    $erro = ApiResponse::montar(true, 'Dados invalidos.', null, [
        'erros' => ['nome' => 'Informe o nome.'],
    ]);

    $teste->assertTrue($erro['erro'], 'Resposta de erro deve marcar erro.');
    $teste->assertEquals(null, $erro['dados'], 'Resposta de erro pode retornar dados nulos.');
    $teste->assertArrayHasKey('erros', $erro, 'Resposta de erro com validacoes deve conter a chave erros.');
    $teste->assertArrayHasKey('nome', $erro['erros'], 'Resposta deve preservar erros por campo.');
}
