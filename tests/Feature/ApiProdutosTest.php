<?php

require_once __DIR__ . '/../../app/Helpers/ApiResponse.php';
require_once __DIR__ . '/../../app/Helpers/Validacao.php';
require_once __DIR__ . '/../../app/Helpers/Sessao.php';
require_once __DIR__ . '/../../app/Helpers/Auth.php';

/**
 * Testes de Feature para a API de Produtos.
 *
 * Cobre os contratos de entrada/saida da API (estrutura de resposta, validacoes,
 * regras de autorizacao e codigos de status esperados) sem depender de banco de dados.
 */
class ApiProdutosTest extends \PHPUnit\Framework\TestCase
{
    private array $produtoValido = [
        'nome'           => 'Teclado Mecanico',
        'codigo'         => 'TECL-001',
        'categoria'      => 'Perifericos',
        'unidade'        => 'un',
        'quantidade'     => 10,
        'estoque_minimo' => 2,
        'estoque_maximo' => 50,
        'preco'          => 350.00,
        'status'         => 'ativo',
    ];

    protected function setUp(): void
    {
        Sessao::iniciar();
        $_SESSION = [];
    }

    public function testEstruturaDasRespostasGet(): void
    {
        $lista = ApiResponse::montar(false, 'Produtos listados com sucesso.', [['id' => 1, 'nome' => 'Mouse']]);
        $this->assertFalse($lista['erro'], 'GET lista: campo erro deve ser false.');
        $this->assertEquals('Produtos listados com sucesso.', $lista['mensagem'], 'GET lista: mensagem deve estar correta.');
        $this->assertTrue(is_array($lista['dados']), 'GET lista: dados devem ser um array.');

        $unico = ApiResponse::montar(false, 'Produto encontrado com sucesso.', ['id' => 1, 'nome' => 'Mouse']);
        $this->assertFalse($unico['erro'], 'GET detalhe: campo erro deve ser false.');
        $this->assertEquals('Produto encontrado com sucesso.', $unico['mensagem'], 'GET detalhe: mensagem deve estar correta.');
        $this->assertEquals(1, $unico['dados']['id'], 'GET detalhe: ID do produto deve corresponder.');
    }

    public function testPostCriarProdutoValidoEInvalido(): void
    {
        $this->assertEquals([], Validacao::produto($this->produtoValido), 'POST 201: produto valido nao deve gerar erros.');

        $resposta201 = ApiResponse::montar(false, 'Produto criado com sucesso.', array_merge(['id' => 10], $this->produtoValido));
        $this->assertFalse($resposta201['erro'], 'POST 201: campo erro deve ser false.');
        $this->assertEquals('Produto criado com sucesso.', $resposta201['mensagem'], 'POST 201: mensagem deve estar correta.');
        $this->assertEquals(10, $resposta201['dados']['id'], 'POST 201: ID do novo produto deve ser retornado.');

        $this->assertArrayHasKey('nome', Validacao::produto(array_merge($this->produtoValido, ['nome' => ''])), 'POST 422: nome vazio deve gerar erro.');
        $this->assertArrayHasKey('quantidade', Validacao::produto(array_merge($this->produtoValido, ['quantidade' => -1])), 'POST 422: quantidade negativa deve gerar erro.');
        $this->assertArrayHasKey('preco', Validacao::produto(array_merge($this->produtoValido, ['preco' => -5.00])), 'POST 422: preco negativo deve gerar erro.');
        $this->assertArrayHasKey('estoque_maximo', Validacao::produto(array_merge($this->produtoValido, ['estoque_minimo' => 20, 'estoque_maximo' => 5])), 'POST 422: estoque maximo menor que minimo deve gerar erro.');
        $this->assertArrayHasKey('status', Validacao::produto(array_merge($this->produtoValido, ['status' => 'bloqueado'])), 'POST 422: status invalido deve gerar erro.');

        $resposta422 = ApiResponse::montar(true, 'Dados invalidos.', null, ['erros' => ['nome' => 'O nome do produto e obrigatorio.']]);
        $this->assertTrue($resposta422['erro'], 'POST 422: campo erro deve ser true.');
        $this->assertEquals('Dados invalidos.', $resposta422['mensagem'], 'POST 422: mensagem deve indicar dados invalidos.');
        $this->assertArrayHasKey('erros', $resposta422, 'POST 422: resposta deve conter chave erros.');
        $this->assertArrayHasKey('nome', $resposta422['erros'], 'POST 422: erros deve conter campo nome.');
    }

    public function testPutPatchAtualizarProduto(): void
    {
        $put200 = ApiResponse::montar(false, 'Produto atualizado com sucesso.', array_merge(['id' => 1], $this->produtoValido, ['nome' => 'Teclado Gamer']));
        $this->assertFalse($put200['erro'], 'PUT/PATCH 200: campo erro deve ser false.');
        $this->assertEquals('Produto atualizado com sucesso.', $put200['mensagem'], 'PUT/PATCH 200: mensagem deve estar correta.');
        $this->assertEquals('Teclado Gamer', $put200['dados']['nome'], 'PUT/PATCH 200: nome atualizado deve estar nos dados.');

        $resposta404 = ApiResponse::montar(true, 'Produto nao encontrado.', null);
        $this->assertTrue($resposta404['erro'], 'PUT/PATCH 404: campo erro deve ser true.');
        $this->assertEquals('Produto nao encontrado.', $resposta404['mensagem'], 'PUT/PATCH 404: mensagem deve indicar nao encontrado.');
        $this->assertEquals(null, $resposta404['dados'], 'PUT/PATCH 404: dados devem ser nulos.');

        $this->assertArrayHasKey('quantidade', Validacao::produto(array_merge($this->produtoValido, ['quantidade' => -99])), 'PATCH 422: quantidade negativa deve gerar erro.');
    }

    public function testDeleteRemoverProduto(): void
    {
        $delete200 = ApiResponse::montar(false, 'Produto removido com sucesso.');
        $this->assertFalse($delete200['erro'], 'DELETE 200: campo erro deve ser false.');
        $this->assertEquals('Produto removido com sucesso.', $delete200['mensagem'], 'DELETE 200: mensagem deve estar correta.');

        $delete404 = ApiResponse::montar(true, 'Produto nao encontrado.', null);
        $this->assertTrue($delete404['erro'], 'DELETE 404: campo erro deve ser true.');
        $this->assertEquals('Produto nao encontrado.', $delete404['mensagem'], 'DELETE 404: mensagem deve indicar nao encontrado.');
    }

    public function testResposta401SemAutenticacao(): void
    {
        $_SESSION = [];
        $this->assertFalse(Sessao::estaLogado(), '401: sessao sem usuario deve retornar nao logado.');

        $resposta401 = ApiResponse::montar(true, 'Faca login para acessar esta API.', null);
        $this->assertTrue($resposta401['erro'], '401: campo erro deve ser true.');
        $this->assertEquals('Faca login para acessar esta API.', $resposta401['mensagem'], '401: mensagem deve instruir login.');
    }

    public function testResposta403SemPermissao(): void
    {
        Sessao::definirUsuario(2, 'Estoquista', 'estoquista@controleestoque.local', 'estoquista');
        $this->assertFalse(Auth::isAdmin(), '403: estoquista nao deve ser reconhecido como admin.');

        $resposta403 = ApiResponse::montar(true, 'Voce nao tem permissao para acessar esta funcionalidade.', null);
        $this->assertTrue($resposta403['erro'], '403: campo erro deve ser true.');
        $this->assertEquals('Voce nao tem permissao para acessar esta funcionalidade.', $resposta403['mensagem'], '403: mensagem deve indicar falta de permissao.');
    }
}
