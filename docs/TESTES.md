# Testes

Os testes da Sprint 3 foram criados em PHP puro, sem Composer ou framework externo.

## Como executar

Na raiz do projeto, rode:

```bash
C:\xampp\php\php.exe tests\run_tests.php
```

Tambem e possivel usar:

```bash
php tests/run_tests.php
```

desde que o PHP esteja disponivel no `PATH`.

## Saida esperada

```text
Todos os testes passaram. Total de assercoes: 27
```

## Arquivos de teste

- `tests/AuthTest.php`: valida `password_hash`, `password_verify` e dados de sessao.
- `tests/PermissaoTest.php`: valida diferenca entre `admin` e `estoquista`.
- `tests/ProdutoApiTest.php`: valida formato padronizado da API e regras de produto.
- `tests/MovimentacaoApiTest.php`: valida regras de entrada e saida de estoque.
- `tests/TestCase.php`: helper simples de assercoes.
- `tests/run_tests.php`: executor dos testes.

## O que os testes cobrem

- Senha correta passa em `password_verify`.
- Senha incorreta nao passa.
- Sessao guarda `id`, `nome`, `email` e `papel`.
- Sessao nao guarda senha.
- Usuario `admin` e reconhecido como administrador.
- Usuario `estoquista` nao e reconhecido como administrador.
- Resposta JSON contem `erro`, `mensagem` e `dados`.
- Produto sem nome e rejeitado.
- Produto com quantidade negativa e rejeitado.
- Produto com estoque maximo menor que minimo e rejeitado.
- Produto com status invalido e rejeitado.
- Movimentacao sem produto, tipo, motivo ou quantidade valida e rejeitada.
