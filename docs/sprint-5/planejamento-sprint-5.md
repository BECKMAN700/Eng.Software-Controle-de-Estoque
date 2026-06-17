# Planejamento - Sprint 5

## Relatorios, Dashboard e Refinamento do Produto

**Disciplina:** Engenharia de Software - UFT
**Periodo:** Junho de 2026
**Sprint:** 5

## 1. Objetivo

Transformar os dados do estoque em informacao gerencial. Alem de registrar produtos e movimentacoes, o sistema passa a oferecer relatorios, graficos, filtros e exportacoes que apoiam decisoes de gestao.

## 2. Criterio de sucesso

Ao final da sprint, o projeto deve apresentar relatorios e dashboard funcionando com dados reais, repositorio organizado, documentacao atualizada, testes revisados e entrega clara para apresentacao.

## 3. Escopo (funcionalidades planejadas)

- Relatorio de giro de estoque (produtos mais e menos movimentados).
- Relatorio de valorizacao total do estoque (quantidade x preco).
- Relatorio de movimentacoes por periodo (intervalo de datas).
- Dashboard gerencial com cards e graficos.
- Exportacao de relatorios em PDF e CSV.
- Filtros avancados por periodo e categoria.
- Refinamento visual e mensagens de erro e sucesso.

## 4. Backlog priorizado

| Codigo | Item | Prioridade |
| --- | --- | --- |
| S5-01 | Relatorio de giro de estoque | Alta |
| S5-02 | Relatorio de valorizacao total | Alta |
| S5-03 | Relatorio de movimentacoes por periodo | Alta |
| S5-04 | Dashboard gerencial | Alta |
| S5-05 | Exportacao em PDF | Media |
| S5-06 | Exportacao em CSV | Media |
| S5-07 | Filtros avancados | Alta |
| S5-08 | Refinamento visual e usabilidade | Alta |
| S5-09 | Melhorias no repositorio | Alta |
| S5-10 | Revisao final e release | Alta |

## 5. Divisao de responsabilidades

| Integrante | Papel | Branch |
| --- | --- | --- |
| Giordano Bruno | Relatorios e exportacao | feature/relatorios-exportacao |
| Murillo Fernandes de Oliveira | Dashboard e graficos | feature/dashboard-gerencial |
| Matheus Sulino da Silva Costa | Filtros avancados e refinamento | feature/filtros-refinamento |
| Joao Pedro Rodrigues Bequiman | Repositorio, documentacao, testes e verificacao final | feature/docs-testes-verificacao |

## 6. Criterios de aceite

- Relatorio de giro mostra produtos mais e menos movimentados.
- Relatorio de valorizacao calcula o valor total do estoque.
- Relatorio por periodo filtra movimentacoes por data inicial e final.
- Dashboard mostra cards e graficos com dados reais.
- Pelo menos um relatorio exporta em PDF e outro em CSV.
- Existem filtros funcionais por periodo e categoria.
- Telas principais estao mais claras e padronizadas.
- Testes existentes continuam passando.
- README e documentacao da sprint atualizados.

## 7. Fluxo de trabalho

Cada integrante cria sua branch a partir da `develop`, faz commits claros, abre Pull Request para a `develop` e aguarda revisao de outro integrante. O merge so acontece apos revisao, testes locais e ausencia de conflitos.
