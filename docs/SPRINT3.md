# Sprint 3 - API JSON de produtos e movimentacoes

## Parte de API do controle de estoque

Esta entrega adiciona uma API em PHP nativo para consultar, criar, atualizar, remover e movimentar produtos com respostas em JSON.

## Entregas realizadas

- Rota `index.php?acao=api_produtos` para listar produtos em JSON.
- Atalho `public/api/produtos.php` para listar produtos em JSON.
- Rota `index.php?acao=api_produtos&id=1` para consultar um produto com `historico_movimentacoes`.
- Atalho `public/api/produto.php?id=1` para consultar um produto com `historico_movimentacoes`.
- `POST` em `api_produtos` para criar produtos com retorno do item criado.
- `PUT` em `api_produtos` para atualizar todos os dados de um produto.
- `PATCH` em `api_produtos` para atualizar apenas os campos enviados.
- `DELETE` em `api_produtos` para remover produtos.
- Rota `index.php?acao=api_movimentacoes` para listar movimentacoes em JSON.
- Atalho `public/api/movimentacoes.php` para listar movimentacoes em JSON.
- Rota `index.php?acao=api_movimentacoes&produto_id=1` para consultar movimentacoes de um produto.
- `POST` em `api_movimentacoes` para registrar movimentacoes.
- Protecao por autenticacao nas rotas da API.
- Protecao por perfil `admin` nas rotas de escrita de produtos.
- Tratamento de erros com respostas JSON validas, sem expor fatal error ou HTML na API.
- Helper `app/Helpers/ApiResponse.php` criado para padronizar respostas JSON.
- Helper `app/Helpers/Validacao.php` criado para validar produtos e movimentacoes.
- Pasta `tests` criada com testes simples em PHP para autenticacao, permissao, API de produtos e API de movimentacoes.
- `setup.php` conecta sem selecionar banco antes de importar o schema, permitindo criar o banco quando ele ainda nao existe.

## Regras validadas

- Requisicoes sem login retornam `401`.
- Requisicoes de escrita sem perfil `admin` retornam `403`.
- Produto inexistente retorna `404` em JSON.
- Dados invalidos retornam `422` em JSON.
- `PATCH` atualiza apenas os campos enviados.
- `GET api_movimentacoes` rejeita `limite` menor ou igual a zero.
- As respostas JSON seguem o padrao `erro`, `mensagem` e `dados`.
- Campos obrigatorios de produto e movimentacao sao validados antes da execucao.

## Testes PHP

Os testes ficam na pasta `tests` e podem ser executados pelo terminal:

```bash
php tests/run_tests.php
```

Tambem e possivel usar o PHP do XAMPP diretamente:

```bash
C:\xampp\php\php.exe tests\run_tests.php
```

Testes incluidos:

- `tests/AuthTest.php`: senha com `password_hash`/`password_verify` e dados de sessao.
- `tests/PermissaoTest.php`: diferenca entre `admin` e `estoquista`.
- `tests/ProdutoApiTest.php`: formato padronizado da resposta JSON e validacoes de produto.
- `tests/MovimentacaoApiTest.php`: validacoes de movimentacao de entrada e saida.

## Como testar esta parte

1. Abrir o sistema no XAMPP e fazer login com um usuario valido.
2. Acessar `index.php?acao=api_produtos` ou `api/produtos.php` e confirmar a lista em JSON.
3. Acessar `index.php?acao=api_produtos&id=1` ou `api/produto.php?id=1` e confirmar o produto com historico.
4. Enviar `POST` para `index.php?acao=api_produtos` com um novo produto e conferir se o retorno traz o novo `id`.
5. Enviar `PATCH` para `index.php?acao=api_produtos&id=1` com apenas um campo, confirmando que os demais dados nao sao apagados.
6. Enviar `GET` para `index.php?acao=api_movimentacoes&limite=-1` e confirmar retorno `422` em JSON.
7. Enviar `DELETE` para `index.php?acao=api_movimentacoes` e confirmar retorno `405` em JSON.
8. Rodar `php tests/run_tests.php` e confirmar que todos os testes passam.

## Observacao

Esta sprint complementa a base web do sistema com uma camada de consumo via API JSON para integracoes e testes automatizados.
