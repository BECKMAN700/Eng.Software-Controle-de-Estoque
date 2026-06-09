# Planejamento - Sprint 4

## Inventario e Auditoria + Testes Unitarios

**Disciplina:** Engenharia de Software - UFT
**Periodo:** Maio de 2026
**Release:** v1.1.0
**Sprint:** 4
**Guia base:** Planejamento Evolutivo - Sprint 4 e Testes Unitarios

## 1. Objetivo

Implementar o modulo de inventario e auditoria para conferencia fisica do estoque, identificacao de divergencias e registro de auditorias, garantindo precisao e confiabilidade dos dados. Em paralelo, acrescentar testes unitarios e documentacao de execucao (item 4 da disciplina).

## 2. Escopo

- Abrir inventario com todos os produtos ativos ou por categoria.
- Salvar a quantidade do sistema no momento da abertura.
- Registrar contagens fisicas manuais.
- Calcular divergencias entre sistema e contagem.
- Relatorio de divergencias com totais (falta, sobra, conferidos, pendentes).
- Aprovar ajustes apenas com usuario admin.
- Atualizar a quantidade do produto e registrar auditoria.
- Testes unitarios das regras do inventario.

## 3. Historias de usuario

1. Abrir inventario por todos os produtos ou por categoria, salvando a quantidade do sistema.
2. Registrar contagem manual (sem quantidade negativa, com salvamento parcial).
3. Comparar divergencias (sistema x contagem; falta, sobra ou sem diferenca).
4. Aprovar ajustes (apenas admin; atualiza estoque; bloqueia aprovacao sem contagem valida).
5. Registrar auditoria (usuario, quantidade anterior e nova, motivo, data).
6. Consultar relatorio de divergencias antes da aprovacao.

## 4. Divisao de responsabilidades

| Integrante | Branch | Responsabilidade |
| --- | --- | --- |
| Joao Pedro | feature/sprint4-base-inventario | Schema, InventarioModel, executor de testes e integracao final |
| Giordano Bruno | feature/sprint4-abertura-inventario | InventarioController, rotas e tela de abertura |
| Murillo Fernandes | feature/sprint4-contagem-aprovacao | Tela de contagem, calculo de divergencia e aprovacao restrita a admin |
| Matheus Sulino | feature/sprint4-relatorioauditoria-docs | Relatorio de divergencias, auditoria, documentacao e testes |

## 5. Criterios de aceite

- schema.sql contem inventarios, inventario_itens e auditorias_estoque.
- O fluxo abrir, contar, divergir, aprovar e auditar funciona.
- Aprovacao exige admin e inventario aprovado nao aceita nova contagem.
- `php tests/run.php` executa todos os testes sem falha.
- README e docs da Sprint 4 atualizados.

## 6. Tabelas criadas

- `inventarios`
- `inventario_itens`
- `auditorias_estoque`
