# Relatorio - Sprint 3

## API JSON e Testes

**Release:** v1.0.1 (13/05/2026)
**Sprint:** 3

## 1. Resumo

A Sprint 3 adicionou uma API em PHP nativo para consultar, criar, atualizar, remover e movimentar produtos com respostas em JSON, alem de padronizar respostas e validacoes e iniciar os testes automatizados.

## 2. Entregas realizadas

### API de produtos e movimentacoes (feature/api-php-nativo)
- `GET index.php?acao=api_produtos` - lista produtos em JSON.
- `GET index.php?acao=api_produtos&id=1` - consulta um produto com historico de movimentacoes.
- `POST api_produtos` - cria produto (exige admin).
- `PUT api_produtos&id=1` - atualiza todos os dados do produto.
- `PATCH api_produtos&id=1` - atualiza apenas os campos enviados.
- `DELETE api_produtos&id=1` - remove produto.
- `GET index.php?acao=api_movimentacoes` e `&produto_id=1` - lista movimentacoes.
- `POST api_movimentacoes` - registra movimentacoes.
- Atalhos em `public/api/` (produtos.php, produto.php, movimentacoes.php).

### Padronizacao, validacao e testes (feature/testes-validacao-api)
- `app/Helpers/ApiResponse.php` para padronizar respostas JSON (`erro`, `mensagem`, `dados`).
- `app/Helpers/Validacao.php` para validar campos obrigatorios de produto e movimentacao.
- Tratamento de erro para ID invalido, produto nao encontrado, metodo nao permitido e dados incompletos.
- Pasta `tests/` com testes simples em PHP: `AuthTest`, `PermissaoTest`, `ProdutoApiTest`, `MovimentacaoApiTest`.

## 3. Regras validadas

- Requisicoes sem login retornam 401.
- Requisicoes de escrita sem perfil admin retornam 403.
- Produto inexistente retorna 404 em JSON.
- Dados invalidos retornam 422 em JSON.
- PATCH atualiza apenas os campos enviados.
- As respostas seguem o padrao `erro`, `mensagem` e `dados`.

## 4. Testes

A Sprint 3 introduziu o executor de testes em PHP nativo (`tests/run.php`), iniciando com cerca de 30 assercoes que cobrem autenticacao, permissoes, validacoes e contratos da API. A suite foi ampliada nas sprints seguintes.

```bash
php tests/run.php
```

## 5. Evidencias

- Pull Requests #40 (API) e #41 (testes e validacao).
- Release v1.0.1 publicada em 13/05/2026.
- Detalhes da API em [../API.md](../API.md).
- Prints em `docs/sprint-3/evidencias/`.

## 6. Checklist

- [x] API de produtos com GET, POST, PUT, PATCH e DELETE.
- [x] API de movimentacoes com GET e POST.
- [x] Respostas JSON padronizadas (ApiResponse).
- [x] Validacoes de campos obrigatorios.
- [x] Erros 401, 403, 404 e 422 tratados.
- [x] Testes em PHP rodando pelo terminal.
- [x] Release v1.0.1 publicada.
