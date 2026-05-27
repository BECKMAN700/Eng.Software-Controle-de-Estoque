# Testes — Movimentações e Inventário

## Teste 1 — Entrada de estoque

### Objetivo
Validar aumento de estoque.

### Passos
1. Abrir produto
2. Registrar entrada
3. Informar quantidade
4. Confirmar

### Resultado esperado
Quantidade deve aumentar.

---

## Teste 2 — Saída de estoque

### Objetivo
Validar redução de estoque.

### Resultado esperado
Quantidade deve diminuir.

---

## Teste 3 — Estoque abaixo do mínimo

### Objetivo
Validar alerta de estoque baixo.

### Resultado esperado
Produto deve aparecer no relatório de divergências.

---

## Teste 4 — Estoque acima do máximo

### Objetivo
Validar excesso de estoque.

### Resultado esperado
Produto deve aparecer como excesso.

---

## Teste 5 — Saída inválida

### Objetivo
Impedir estoque negativo.

### Resultado esperado
Sistema deve bloquear operação.

---

## Teste 6 — Divergências

### Objetivo
Validar listagem de inconsistências.

### Resultado esperado
Tela deve listar apenas produtos divergentes.