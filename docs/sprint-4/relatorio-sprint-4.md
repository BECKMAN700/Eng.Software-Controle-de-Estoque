# Relatorio - Sprint 4

## Inventario e Auditoria + Testes Unitarios

**Release:** v1.1.0 (26/05/2026)
**Sprint:** 4

## 1. Resumo

A Sprint 4 entregou o modulo de inventario e auditoria do estoque e ampliou os testes automatizados. O sistema passou a permitir conferencia fisica, calculo de divergencias, aprovacao restrita a admin e registro de auditoria de cada ajuste.

## 2. Fluxo implementado

1. O usuario acessa `index.php?acao=inventarios`.
2. Abre um inventario informando titulo, observacao e, opcionalmente, categoria.
3. O sistema grava os produtos ativos do filtro em `inventario_itens`, com a `quantidade_sistema` do momento da abertura.
4. O usuario registra a quantidade fisica contada.
5. O sistema calcula a diferenca entre contagem e quantidade do sistema.
6. O relatorio `inventario_divergencias` mostra faltas, sobras, itens conferidos e pendentes.
7. Apenas administrador aprova os ajustes.
8. Na aprovacao, o sistema atualiza `produtos.quantidade`.
9. Cada ajuste aprovado gera registro em `auditorias_estoque`.

## 3. Regras de seguranca

- Todas as rotas de inventario exigem login.
- Abertura, contagem e consulta podem ser feitas por admin ou estoquista.
- Aprovacao e consulta de auditoria sao restritas a admin.
- POSTs de abertura, contagem e aprovacao usam CSRF.
- Inventario aprovado nao aceita nova contagem.
- Inventario com item pendente nao pode ser aprovado.

## 4. Tabelas e arquivos

Tabelas: `inventarios`, `inventario_itens`, `auditorias_estoque`.

Arquivos principais:
- `app/Controllers/InventarioController.php`
- `app/Models/InventarioModel.php`
- `app/Views/inventarios/` (listar, criar, detalhar, contagem, divergencias, auditoria)
- `database/schema.sql`

## 5. Testes

A suite de testes foi ampliada para 93 assercoes, cobrindo validacoes, permissoes, respostas de API, movimentacoes e regras puras do inventario (calculo de divergencia, bloqueio de contagem negativa e aprovacao restrita a admin).

```bash
C:\xampp\php\php.exe tests\run.php
```

Saida esperada:

```text
Todos os testes passaram. Total de assercoes: 93
```

## 6. Evidencias

- Pull Requests #45 a #49.
- Release v1.1.0 publicada em 26/05/2026.
- Prints em `docs/sprint-4/evidencias/`.

## 7. Checklist final

- [x] schema.sql com inventarios, inventario_itens e auditorias_estoque.
- [x] Inventario pode ser aberto por todos os produtos ou por categoria.
- [x] Quantidade do sistema gravada no momento da abertura.
- [x] Contagem validada (inteiro nao negativo) e salvamento parcial.
- [x] Divergencias com falta, sobra, conferidos e pendentes.
- [x] Aprovacao exige admin e atualiza produtos.quantidade.
- [x] Auditoria registrada em auditorias_estoque.
- [x] POSTs de inventario usam CSRF.
- [x] tests/run.php passou com 93 assercoes.
- [x] README e docs da Sprint 4 atualizados.
