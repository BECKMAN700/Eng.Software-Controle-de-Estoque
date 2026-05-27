# Testes

Os testes do projeto usam PHP puro, sem Composer ou framework externo. O runner central fica em `tests/run_tests.php`, e `tests/run.php` funciona como atalho.

## Como executar

Na raiz do projeto, rode:

```bash
C:\xampp\php\php.exe tests\run.php
```

Tambem e possivel usar:

```bash
C:\xampp\php\php.exe tests\run_tests.php
```

## Saida esperada

```text
Todos os testes passaram. Total de assercoes: 93
```

## Arquivos de teste

- `tests/AuthTest.php`: valida hash de senha e dados basicos de sessao.
- `tests/PermissaoTest.php`: valida diferenca entre `admin` e `estoquista`.
- `tests\ApiResponseTest.php`: valida formato padrao de respostas JSON.
- `tests/ProdutoApiTest.php`: valida regras de produto e contratos de API.
- `tests/MovimentacaoApiTest.php`: valida entrada, saida e bloqueios de movimentacao.
- `tests/InventarioTest.php`: valida calculo de divergencia e contagem invalida.
- `tests/Unit/ValidacaoTest.php`: valida regras de produto e contagem.
- `tests/Feature/ApiProdutosTest.php`: valida cenarios de API de produtos.
- `tests/TestCase.php`: helper simples de assercoes.
- `tests/run_tests.php`: executor principal dos testes.

## O que os testes cobrem

- Autenticacao e dados de sessao.
- Permissoes de admin e estoquista.
- Estrutura padrao de respostas JSON.
- Validacoes de produto.
- Validacoes de movimentacao.
- Regras de contagem de inventario.
- Calculo de falta, sobra e divergencia zerada.
- Cenarios de erro `401`, `403`, `404` e `422` simulados na API.
