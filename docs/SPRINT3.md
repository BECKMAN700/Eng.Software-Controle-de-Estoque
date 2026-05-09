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
- `setup.php` conecta sem selecionar banco antes de importar o schema, permitindo criar o banco quando ele ainda nao existe.

## Regras validadas

- Requisicoes sem login retornam `401`.
- Requisicoes de escrita sem perfil `admin` retornam `403`.
- Produto inexistente retorna `404` em JSON.
- Dados invalidos retornam `422` em JSON.
- `PATCH` atualiza apenas os campos enviados.
- `GET api_movimentacoes` rejeita `limite` menor ou igual a zero.

## Como testar esta parte

1. Abrir o sistema no XAMPP e fazer login com um usuario valido.
2. Acessar `index.php?acao=api_produtos` ou `api/produtos.php` e confirmar a lista em JSON.
3. Acessar `index.php?acao=api_produtos&id=1` ou `api/produto.php?id=1` e confirmar o produto com historico.
4. Enviar `POST` para `index.php?acao=api_produtos` com um novo produto e conferir se o retorno traz o novo `id`.
5. Enviar `PATCH` para `index.php?acao=api_produtos&id=1` com apenas um campo, confirmando que os demais dados nao sao apagados.
6. Enviar `GET` para `index.php?acao=api_movimentacoes&limite=-1` e confirmar retorno `422` em JSON.
7. Enviar `DELETE` para `index.php?acao=api_movimentacoes` e confirmar retorno `405` em JSON.

## Observacao

Esta sprint complementa a base web do sistema com uma camada de consumo via API JSON para integracoes e testes automatizados.
