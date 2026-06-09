# Relatorio - Sprint 1

## Produtos e Movimentacoes de Estoque

**Release:** v1.0.0 (29/04/2026)
**Sprint:** 1

## 1. Resumo

A Sprint 1 entregou a base do Controle de Estoque: o CRUD de produtos, a estrutura do banco e o controle de movimentacoes de entrada e saida, com limites de estoque e alertas. Foi a partir dela que o sistema passou a registrar e movimentar produtos de verdade.

## 2. Entregas realizadas

### Produtos (CRUD)
- Cadastro de produtos com nome, codigo, categoria, unidade, preco e limites.
- Listagem com busca e catalogo de produtos.
- Edicao e exclusao de produtos (com correcoes de reversao nos PRs #4 a #8).

### Movimentacoes de estoque
- Registro de entradas e saidas (`entrada`, `saida`, `movimentar`).
- Atualizacao automatica da quantidade do produto.
- Bloqueio de saida maior que o estoque disponivel.
- Historico de movimentacoes por produto.

### Limites e alertas
- Estoque minimo e maximo por produto.
- Alerta de produtos abaixo do minimo, no minimo e acima do maximo.
- Destaque de estoque critico e reabastecimento.

### Banco de dados
- Tabelas `produtos` e `movimentacoes` em `database/schema.sql`.

## 3. Rotas principais

- `index.php?acao=listar`, `criar`, `salvar`, `editar`, `atualizar`, `excluir`
- `index.php?acao=entrada`, `saida`, `movimentar`, `historico_movimentacoes`
- `index.php?acao=catalogo`, `divergencias` (limites de estoque)

## 4. Evidencias

- Pull Requests #1 a #34.
- Release v1.0.0 publicada em 29/04/2026.
- Testes de movimentacao documentados em [../testes-movimentacoes.md](../testes-movimentacoes.md).
- Prints em `docs/sprint-1/evidencias/`.

## 5. Checklist

- [x] CRUD de produtos funcionando.
- [x] Entradas e saidas atualizam o estoque.
- [x] Saida nao deixa o estoque negativo.
- [x] Historico de movimentacoes disponivel.
- [x] Alertas de estoque minimo, maximo e critico.
- [x] Banco com tabelas produtos e movimentacoes.
- [x] Release v1.0.0 publicada.
