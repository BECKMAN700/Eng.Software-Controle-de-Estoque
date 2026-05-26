<?php

require_once __DIR__ . '/../../app/Helpers/ApiResponse.php';
require_once __DIR__ . '/../../app/Helpers/Validacao.php';
require_once __DIR__ . '/../../app/Helpers/Sessao.php';
require_once __DIR__ . '/../../app/Helpers/Auth.php';

/**
 * Testes de Feature para a API de Produtos.
 *
 * Cobre os contratos de entrada/saída da API (estrutura de resposta, validações,
 * regras de autorização e códigos de status esperados) sem depender de banco de dados,
 * seguindo o mesmo padrão dos demais testes do projeto.
 */
function testeFeatureApiProdutos(TestCase $teste): void
{
    // ─────────────────────────────────────────────────────────
    // BLOCO 1: Estrutura de Respostas (GET/POST genérico)
    // ─────────────────────────────────────────────────────────

    // GET – Sucesso: estrutura correta de listagem
    $respostaGet = ApiResponse::montar(false, 'Produtos listados com sucesso.', [['id' => 1, 'nome' => 'Mouse']]);
    $teste->assertFalse($respostaGet['erro'], 'GET lista: campo erro deve ser false.');
    $teste->assertEquals('Produtos listados com sucesso.', $respostaGet['mensagem'], 'GET lista: mensagem deve estar correta.');
    $teste->assertTrue(is_array($respostaGet['dados']), 'GET lista: dados devem ser um array.');

    // GET – Sucesso: detalhar produto único
    $respostaGetUnico = ApiResponse::montar(false, 'Produto encontrado com sucesso.', ['id' => 1, 'nome' => 'Mouse']);
    $teste->assertFalse($respostaGetUnico['erro'], 'GET detalhe: campo erro deve ser false.');
    $teste->assertEquals('Produto encontrado com sucesso.', $respostaGetUnico['mensagem'], 'GET detalhe: mensagem deve estar correta.');
    $teste->assertEquals(1, $respostaGetUnico['dados']['id'], 'GET detalhe: ID do produto deve corresponder.');

    // ─────────────────────────────────────────────────────────
    // BLOCO 2: POST – Criar produto
    // ─────────────────────────────────────────────────────────

    // POST Sucesso (201): produto válido
    $produtoValido = [
        'nome'           => 'Teclado Mecânico',
        'codigo'         => 'TECL-001',
        'categoria'      => 'Periféricos',
        'unidade'        => 'un',
        'quantidade'     => 10,
        'estoque_minimo' => 2,
        'estoque_maximo' => 50,
        'preco'          => 350.00,
        'status'         => 'ativo',
    ];
    $erros = Validacao::produto($produtoValido);
    $teste->assertEquals([], $erros, 'POST 201: produto valido nao deve gerar erros de validacao.');

    $respostaPost201 = ApiResponse::montar(false, 'Produto criado com sucesso.', array_merge(['id' => 10], $produtoValido));
    $teste->assertFalse($respostaPost201['erro'], 'POST 201: campo erro deve ser false.');
    $teste->assertEquals('Produto criado com sucesso.', $respostaPost201['mensagem'], 'POST 201: mensagem deve estar correta.');
    $teste->assertEquals(10, $respostaPost201['dados']['id'], 'POST 201: ID do novo produto deve ser retornado.');

    // POST Falha (422): nome vazio
    $semNome = array_merge($produtoValido, ['nome' => '']);
    $errosSemNome = Validacao::produto($semNome);
    $teste->assertArrayHasKey('nome', $errosSemNome, 'POST 422: nome vazio deve gerar erro no campo nome.');

    // POST Falha (422): quantidade negativa
    $qtdNegativa = array_merge($produtoValido, ['quantidade' => -1]);
    $errosQtd = Validacao::produto($qtdNegativa);
    $teste->assertArrayHasKey('quantidade', $errosQtd, 'POST 422: quantidade negativa deve gerar erro no campo quantidade.');

    // POST Falha (422): preço negativo
    $precoNegativo = array_merge($produtoValido, ['preco' => -5.00]);
    $errosPreco = Validacao::produto($precoNegativo);
    $teste->assertArrayHasKey('preco', $errosPreco, 'POST 422: preco negativo deve gerar erro no campo preco.');

    // POST Falha (422): estoque maximo < estoque minimo
    $estoqueInvalido = array_merge($produtoValido, ['estoque_minimo' => 20, 'estoque_maximo' => 5]);
    $errosEstoque = Validacao::produto($estoqueInvalido);
    $teste->assertArrayHasKey('estoque_maximo', $errosEstoque, 'POST 422: estoque maximo menor que minimo deve gerar erro.');

    // POST Falha (422): status inválido
    $statusInvalido = array_merge($produtoValido, ['status' => 'bloqueado']);
    $errosStatus = Validacao::produto($statusInvalido);
    $teste->assertArrayHasKey('status', $errosStatus, 'POST 422: status invalido deve gerar erro no campo status.');

    // Estrutura da resposta 422
    $resposta422 = ApiResponse::montar(true, 'Dados invalidos.', null, ['erros' => ['nome' => 'O nome do produto e obrigatorio.']]);
    $teste->assertTrue($resposta422['erro'], 'POST 422: campo erro deve ser true.');
    $teste->assertEquals('Dados invalidos.', $resposta422['mensagem'], 'POST 422: mensagem deve indicar dados invalidos.');
    $teste->assertArrayHasKey('erros', $resposta422, 'POST 422: resposta deve conter chave erros.');
    $teste->assertArrayHasKey('nome', $resposta422['erros'], 'POST 422: erros deve conter campo nome.');

    // ─────────────────────────────────────────────────────────
    // BLOCO 3: PUT / PATCH – Atualizar produto
    // ─────────────────────────────────────────────────────────

    // PUT/PATCH Sucesso (200): produto atualizado
    $respostaPut200 = ApiResponse::montar(false, 'Produto atualizado com sucesso.', array_merge(['id' => 1], $produtoValido, ['nome' => 'Teclado Gamer']));
    $teste->assertFalse($respostaPut200['erro'], 'PUT/PATCH 200: campo erro deve ser false.');
    $teste->assertEquals('Produto atualizado com sucesso.', $respostaPut200['mensagem'], 'PUT/PATCH 200: mensagem deve estar correta.');
    $teste->assertEquals('Teclado Gamer', $respostaPut200['dados']['nome'], 'PUT/PATCH 200: nome atualizado deve estar nos dados.');

    // PUT/PATCH Falha (404): produto nao encontrado
    $resposta404 = ApiResponse::montar(true, 'Produto nao encontrado.', null);
    $teste->assertTrue($resposta404['erro'], 'PUT/PATCH 404: campo erro deve ser true.');
    $teste->assertEquals('Produto nao encontrado.', $resposta404['mensagem'], 'PUT/PATCH 404: mensagem deve indicar nao encontrado.');
    $teste->assertEquals(null, $resposta404['dados'], 'PUT/PATCH 404: dados devem ser nulos.');

    // PATCH Falha (422): campo invalido na atualização parcial
    $patchInvalido = array_merge($produtoValido, ['quantidade' => -99]);
    $errosPatch = Validacao::produto($patchInvalido);
    $teste->assertArrayHasKey('quantidade', $errosPatch, 'PATCH 422: quantidade negativa em atualizacao parcial deve gerar erro.');

    // ─────────────────────────────────────────────────────────
    // BLOCO 4: DELETE – Remover produto
    // ─────────────────────────────────────────────────────────

    // DELETE Sucesso (200)
    $respostaDelete200 = ApiResponse::montar(false, 'Produto removido com sucesso.');
    $teste->assertFalse($respostaDelete200['erro'], 'DELETE 200: campo erro deve ser false.');
    $teste->assertEquals('Produto removido com sucesso.', $respostaDelete200['mensagem'], 'DELETE 200: mensagem deve estar correta.');

    // DELETE Falha (404): produto inexistente
    $respostaDelete404 = ApiResponse::montar(true, 'Produto nao encontrado.', null);
    $teste->assertTrue($respostaDelete404['erro'], 'DELETE 404: campo erro deve ser true.');
    $teste->assertEquals('Produto nao encontrado.', $respostaDelete404['mensagem'], 'DELETE 404: mensagem deve indicar nao encontrado.');

    // ─────────────────────────────────────────────────────────
    // BLOCO 5: 401 – Não autenticado
    // ─────────────────────────────────────────────────────────

    // Simula sessão sem usuário e verifica o estado esperado
    Sessao::iniciar();
    $_SESSION = [];

    $teste->assertFalse(Sessao::estaLogado(), '401: sessao sem usuario deve retornar nao logado.');

    $resposta401 = ApiResponse::montar(true, 'Faca login para acessar esta API.', null);
    $teste->assertTrue($resposta401['erro'], '401: campo erro deve ser true.');
    $teste->assertEquals('Faca login para acessar esta API.', $resposta401['mensagem'], '401: mensagem deve instruir login.');

    // ─────────────────────────────────────────────────────────
    // BLOCO 6: 403 – Sem permissão (estoquista)
    // ─────────────────────────────────────────────────────────

    Sessao::definirUsuario(2, 'Estoquista', 'estoquista@controleestoque.local', 'estoquista');
    $teste->assertFalse(Auth::isAdmin(), '403: estoquista nao deve ser reconhecido como admin.');

    $resposta403 = ApiResponse::montar(true, 'Voce nao tem permissao para acessar esta funcionalidade.', null);
    $teste->assertTrue($resposta403['erro'], '403: campo erro deve ser true.');
    $teste->assertEquals(
        'Voce nao tem permissao para acessar esta funcionalidade.',
        $resposta403['mensagem'],
        '403: mensagem deve indicar falta de permissao.'
    );

    // Restaura sessão limpa ao final
    $_SESSION = [];
}
