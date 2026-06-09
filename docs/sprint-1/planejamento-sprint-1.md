# Planejamento - Sprint 1

## Produtos e Movimentacoes de Estoque

**Disciplina:** Engenharia de Software - UFT
**Periodo:** Marco a Abril de 2026
**Release:** v1.0.0
**Sprint:** 1

## 1. Objetivo

Construir a base do sistema de Controle de Estoque: o cadastro de produtos e o controle das movimentacoes de entrada e saida, com limites de estoque e alertas. Esta sprint cria a fundacao reutilizada por todas as sprints seguintes.

## 2. Escopo (funcionalidades planejadas)

- Cadastro, listagem, edicao e exclusao de produtos (CRUD).
- Estrutura inicial do banco de dados (tabelas produtos e movimentacoes).
- Registro de entradas e saidas de estoque.
- Historico de movimentacoes por produto.
- Limites de estoque minimo e maximo.
- Alertas de estoque minimo e estoque critico.
- Catalogo e telas iniciais do sistema.

## 3. Backlog (mapeado dos Pull Requests)

| Item | Pull Requests |
| --- | --- |
| Cadastro de produtos | #1 |
| Listagem de produtos | #2, #33 |
| Editar produto | #3 |
| Excluir produto (com reversoes) | #4 a #8 |
| Movimentacao de estoque | #9 |
| Completar campos do produto | #13, #16 |
| Saida de estoque | #14, #15 |
| Historico de movimentacoes | #17 |
| Entrada de estoque | #18, #20, #21 |
| Banco de dados | #23 |
| Reabastecimento | #28 |
| Estoque minimo e maximo | #29 |
| Alerta de minimo | #31 |
| Estoque critico | #32 |
| Front inicial | #34 |

## 4. Divisao de responsabilidades

Esta foi a sprint de fundacao do projeto, com participacao de toda a equipe na construcao do CRUD e das movimentacoes. Joao Pedro concentrou a maior parte da estrutura inicial (banco, controllers e integracao), com contribuicoes dos demais integrantes nas telas e regras de estoque.

## 5. Criterios de aceite

- Produtos podem ser cadastrados, listados, editados e excluidos.
- Entradas e saidas atualizam a quantidade do produto.
- Saida nao pode deixar o estoque negativo.
- Historico registra cada movimentacao.
- Produtos abaixo do minimo aparecem como alerta.
- O sistema abre pelo XAMPP sem erro.

## 6. Tecnologias

PHP 8.x nativo, MySQL, PDO, HTML5, CSS3, arquitetura MVC, XAMPP, Git e GitHub com GitFlow.
