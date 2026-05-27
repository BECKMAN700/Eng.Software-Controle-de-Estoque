# Sprint 4 - Inventario e Auditoria

## Objetivo

Registrar o fluxo de inventario implementado na Sprint 4 e as regras de auditoria usadas quando um administrador aprova ajustes de estoque.

## Fluxo implementado

1. O usuario acessa `index.php?acao=inventarios`.
2. O usuario abre um inventario informando titulo, observacao e, opcionalmente, categoria.
3. O sistema grava os produtos ativos do filtro escolhido em `inventario_itens`.
4. Cada item recebe a `quantidade_sistema` existente no momento da abertura.
5. O usuario registra a quantidade fisica contada em `inventario_contagem`.
6. O sistema calcula a diferenca entre quantidade contada e quantidade do sistema.
7. O relatorio `inventario_divergencias` mostra faltas, sobras, itens conferidos e pendentes.
8. Apenas administrador pode aprovar os ajustes.
9. Na aprovacao, o sistema atualiza `produtos.quantidade`.
10. Cada ajuste aprovado gera registro em `auditorias_estoque`.

## Regras de seguranca

- Todas as rotas de inventario exigem login.
- Abertura, contagem e consulta podem ser feitas por admin ou estoquista.
- Aprovacao e consulta de auditoria sao restritas a admin.
- POSTs de abertura, contagem e aprovacao usam CSRF.
- Inventario aprovado nao aceita nova contagem.
- Inventario com item pendente nao pode ser aprovado.

## Tabelas da Sprint 4

- `inventarios`
- `inventario_itens`
- `auditorias_estoque`

## Arquivos principais

- `app/Controllers/InventarioController.php`
- `app/Models/InventarioModel.php`
- `app/Views/inventarios/listar.php`
- `app/Views/inventarios/criar.php`
- `app/Views/inventarios/detalhar.php`
- `app/Views/inventarios/contagem.php`
- `app/Views/inventarios/divergencias.php`
- `app/Views/inventarios/auditoria.php`
- `database/schema.sql`

## Testes

Execute:

```bash
C:\xampp\php\php.exe tests\run.php
```

Os testes cobrem validacoes, permissoes, respostas de API, movimentacoes e regras puras do inventario.
