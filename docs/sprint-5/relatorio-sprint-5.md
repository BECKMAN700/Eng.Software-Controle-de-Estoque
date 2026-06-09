# Relatorio - Sprint 5

## Relatorios, Dashboard e Refinamento do Produto

**Disciplina:** Engenharia de Software - UFT
**Periodo:** Junho de 2026
**Sprint:** 5

## 1. Resumo

A Sprint 5 entregou a camada gerencial do sistema: relatorios com dados reais, exportacao em PDF e CSV, dashboard com graficos e filtros avancados, alem de refinamento visual e revisao final do repositorio.

## 2. Entregas por integrante

### Giordano Bruno - Relatorios e exportacao (PR #51)

- Relatorio de giro de estoque (`giroEstoque`): soma de entradas e saidas por produto, com classificacao em alto, medio e baixo giro.
- Relatorio de valorizacao (`valorizacao`): quantidade x preco por produto e total geral.
- Relatorio de movimentacoes por periodo (`movimentacoesPeriodo`): filtro por data inicial e final, tipo, produto e categoria.
- Exportacao em PDF (FPDF) e CSV dos tres relatorios.
- Consultas no model `Relatorio.php` com PDO e prepared statements.

Status: completo, acima do minimo exigido (PDF e CSV nos tres relatorios).

### Murillo Fernandes de Oliveira - Dashboard e graficos (PR #52)

- Tela de dashboard (`dashboard`) acessivel pelo menu lateral.
- Cards: produtos cadastrados, unidades em estoque, valor do estoque, produtos criticos, entradas e saidas dos ultimos 30 dias.
- Graficos com Chart.js: entradas x saidas, produtos mais movimentados e tendencia de movimentacoes (7 dias).
- Model `Dashboard.php` com consultas reais ao banco.

Status: completo. Dois bugs corrigidos na revisao final (ver secao 4).

### Matheus Sulino da Silva Costa - Filtros e refinamento (PR #53)

- Filtros por periodo e categoria nos produtos e nos relatorios.
- Validacao estrita de datas em `Validacao::periodoRelatorio` (bloqueia datas invalidas e data final menor que a inicial).
- Refinamento visual de tabelas, badges, situacao de estoque e mensagens.

Status: completo no escopo. O filtro de fornecedor/deposito nao foi implementado por nao haver estrutura no banco, conforme orientacao do planejamento (nao criar filtro falso).

### Joao Pedro Rodrigues Bequiman - Repositorio, documentacao, testes e verificacao final (PR #54 e PR de docs)

- Revisao dos PRs dos colegas e correcao de bugs (secao 4).
- Atualizacao do README com a Sprint 5.
- Documentacao da sprint em `docs/sprint-5/`.
- Revisao e execucao dos testes.
- Verificacao final e apoio ao merge na `develop`.

## 3. Rotas adicionadas na Sprint 5

- `index.php?acao=dashboard`
- `index.php?acao=relatorios`
- `index.php?acao=giro_estoque`
- `index.php?acao=valorizacao`
- `index.php?acao=movimentacoes_periodo`
- `index.php?acao=exportar-pdf&relatorio=...`
- `index.php?acao=exportar-csv&relatorio=...`

Observacao: a Sprint 5 nao exigiu mudanca de schema. A coluna `preco` ja existia em `produtos` e a coluna `usuario_id` em `movimentacoes` e criada de forma segura pelo model de relatorios quando ausente.

## 4. Correcoes feitas na revisao final (PR #54)

- `dashboard/index.php`: a secao de produtos criticos tinha um bloco aninhado duplicado que renderizava a tabela uma vez por produto. Corrigido para renderizar uma unica tabela.
- `partials/sidebar.php`: o link "Dashboard" estava sem fechamento `</a>`. Corrigido.
- `RelatorioController.php`: a regra de classificacao de giro estava duplicada em tres metodos. Centralizada no helper `classificarGiro()`.
- `RelatorioController.php`: troca de `utf8_decode()` (descontinuado no PHP 8.2+) por `mb_convert_encoding()` no fallback de acentuacao das exportacoes.

## 5. Evidencias de testes

Suite executada com o PHP do XAMPP:

```bash
C:\xampp\php\php.exe tests\run_tests.php
```

Saida:

```text
Todos os testes passaram. Total de assercoes: 97
```

Todos os arquivos PHP alterados passaram no lint (`php -l`). Prints das telas em `docs/sprint-5/evidencias/`.

## 6. Checklist final

- [x] Relatorio de giro lista produtos mais e menos movimentados.
- [x] Relatorio de valorizacao calcula o valor total do estoque.
- [x] Relatorio por periodo filtra por data inicial e final.
- [x] Dashboard abre pelo menu e mostra cards e graficos com dados reais.
- [x] Exportacao em PDF e CSV disponivel nos tres relatorios.
- [x] Filtros por periodo e categoria funcionam sem quebrar a tela.
- [x] Refinamento visual aplicado nas telas principais.
- [x] Testes existentes continuam passando (97 assercoes).
- [x] README atualizado.
- [x] Pasta docs/sprint-5 criada com planejamento, relatorio e evidencias.
- [x] Pull requests revisados por integrante diferente do autor.
- [ ] Evidencias (prints) anexadas em docs/sprint-5/evidencias/ (capturar com o sistema rodando).
- [ ] Develop estavel e pronta para a release final.

## 7. Observacoes para a apresentacao

- O dashboard usa Chart.js via CDN; garantir conexao com a internet na apresentacao.
- O relatorio de giro com filtro de periodo mostra apenas produtos movimentados no intervalo selecionado.
