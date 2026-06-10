# Testes

Os testes do projeto usam o **PHPUnit 9.6**, framework de testes padrao do PHP, gerenciado pelo **Composer**. A aplicacao continua em PHP nativo; o PHPUnit entra apenas como dependencia de desenvolvimento (`require-dev`).

## Requisitos

- PHP 8.0+ com a extensao `mbstring` (o PHP do XAMPP ja inclui).
- Composer. O arquivo `composer.phar` esta incluido no projeto para quem nao tem o Composer instalado globalmente.

## Instalacao

Na primeira vez, instale as dependencias de teste:

```bash
C:\xampp\php\php.exe composer.phar install
```

Isso cria a pasta `vendor/` (ignorada no Git) com o PHPUnit.

## Como executar

Todos os testes:

```bash
C:\xampp\php\php.exe composer.phar test
```

Saida detalhada (cada teste como uma frase):

```bash
C:\xampp\php\php.exe vendor\phpunit\phpunit\phpunit --testdox
```

Por grupo:

```bash
C:\xampp\php\php.exe composer.phar test-unit      # testes unitarios
C:\xampp\php\php.exe composer.phar test-feature   # testes de feature
```

## Saida esperada

```text
OK (25 tests, 97 assertions)
```

## Estrutura dos testes

- `phpunit.xml` - configuracao do PHPUnit e dos grupos de teste.
- `tests/bootstrap.php` - preparo do ambiente (sessao em modo CLI).
- `tests/Unit/ValidacaoTest.php` - validacao de produto, contagem de inventario e periodo de relatorio.
- `tests/AuthTest.php` - hash de senha, sessao e token CSRF.
- `tests/PermissaoTest.php` - papeis admin e estoquista.
- `tests/ApiResponseTest.php` - formato padrao das respostas JSON.
- `tests/ProdutoApiTest.php` - contratos e validacoes da API de produtos.
- `tests/MovimentacaoApiTest.php` - validacoes de entrada e saida.
- `tests/InventarioTest.php` - calculo de divergencia e contagem.
- `tests/Feature/ApiProdutosTest.php` - cenarios da API de produtos (GET, POST, PUT/PATCH, DELETE, 401, 403).

## Cobertura

Autenticacao, sessao, permissoes, respostas JSON, validacoes de produto e movimentacao, regras de inventario e contratos da API. Os testes nao dependem de banco de dados, rodando de forma rapida e isolada.
