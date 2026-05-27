# Auditoria — Sprint 4

## Objetivo
Registrar alterações relacionadas à Sprint 4.

---

## Funcionalidades adicionadas

### Relatório de divergências
- Criação da tela divergencias.php
- Identificação de produtos abaixo do estoque mínimo
- Identificação de produtos acima do estoque máximo

### Auditoria de movimentações
- Validação de entradas e saídas
- Controle de estoque mínimo e máximo
- Histórico de movimentações preservado

### Melhorias visuais
- Destaque visual para divergências
- Separação entre produtos abaixo e acima do limite

---

## Arquivos alterados

### Controllers
- ProdutoController.php

### Models
- ProdutoModel.php

### Views
- listar.php
- divergencias.php

### Rotas
- public/index.php

---

## Data
Sprint 4 — Controle de Estoque

# Sprint 4 — Relatórios, Auditoria e Testes

## Funcionalidades implementadas

### Relatório de divergências
Permite visualizar produtos:
- abaixo do estoque mínimo
- acima do estoque máximo

### Auditoria
Foi adicionada documentação de auditoria contendo:
- arquivos alterados
- funcionalidades implementadas
- rastreamento das alterações

### Testes
Foram criados cenários de testes para:
- entrada de estoque
- saída de estoque
- estoque abaixo do mínimo
- estoque acima do máximo
- movimentações inválidas

---

## Fluxo
1. Produto é cadastrado
2. Estoque mínimo e máximo são definidos
3. Movimentações alteram quantidade
4. Sistema verifica divergências
5. Relatório exibe inconsistências

---

## Melhorias futuras
- Exportação PDF
- Dashboard gráfico
- Logs completos de auditoria
- Controle de usuários