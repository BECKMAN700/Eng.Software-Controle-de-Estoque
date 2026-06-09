# Planejamento - Sprint 3

## API JSON e Testes

**Disciplina:** Engenharia de Software - UFT
**Periodo:** Maio de 2026
**Release:** v1.0.1
**Sprint:** 3
**Guia base:** Guia de trabalho das Sprints 2 e 3

## 1. Objetivo

Expor os dados do sistema por uma API em PHP nativo (JSON) para produtos e movimentacoes, padronizar as respostas e validacoes e iniciar os testes automatizados. A API mostra que o sistema pode ser consumido por navegador, Postman ou outra aplicacao.

## 2. Escopo

- Endpoints JSON para produtos (GET, POST, PUT, PATCH, DELETE).
- Endpoints JSON para movimentacoes (GET, POST).
- Helper de resposta JSON padronizada (ApiResponse).
- Helper de validacao de produtos e movimentacoes (Validacao).
- Tratamento de erros: 401, 403, 404 e 422.
- Testes simples em PHP para autenticacao, permissoes, produtos e movimentacoes.

## 3. Divisao de responsabilidades

| Integrante | Branch | Responsabilidade |
| --- | --- | --- |
| Iagor Lourenco | feature/api-php-nativo | APIs em PHP nativo para produtos e movimentacoes |
| Matheus Sulino | feature/testes-validacao-api | Padronizacao da API, validacoes, testes e documentacao tecnica |

> Iagor Lourenco participou desta sprint e depois saiu do grupo; as entregas dele foram mantidas e evoluidas pelos demais integrantes.

## 4. Criterios de aceite

- A API abre no navegador ou Postman e retorna JSON valido.
- As respostas seguem o padrao `erro`, `mensagem` e `dados`.
- A API nao retorna HTML nem warning do PHP junto com o JSON.
- Sem login retorna 401; sem permissao retorna 403.
- Produto inexistente retorna 404; dados invalidos retornam 422.
- Os testes rodam pelo terminal com `php tests/run.php`.

## 5. Regra de trabalho

Branches a partir da develop, PR para develop e revisao cruzada. Ninguem aprova o proprio PR.
