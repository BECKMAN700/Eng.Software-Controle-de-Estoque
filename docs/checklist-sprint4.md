# Checklist final - Sprint 4

## Inventario e auditoria

- [x] Tabelas `inventarios`, `inventario_itens` e `auditorias_estoque` existem no `database/schema.sql`.
- [x] Inventario pode ser aberto com todos os produtos ativos ou por categoria.
- [x] A quantidade do sistema e gravada no momento da abertura.
- [x] Contagens fisicas podem ser salvas com validacao de inteiro nao negativo.
- [x] Observacoes por item de inventario sao persistidas.
- [x] Divergencias exibem falta, sobra, itens conferidos e itens pendentes.
- [x] Aprovacao exige usuario admin.
- [x] Inventario com contagem pendente nao pode ser aprovado.
- [x] Aprovacao atualiza `produtos.quantidade`.
- [x] Aprovacao registra auditoria em `auditorias_estoque`.
- [x] Auditoria pode ser consultada em `inventario_auditoria`.

## Seguranca

- [x] Rotas internas exigem login.
- [x] Aprovacao e consulta de auditoria exigem admin.
- [x] POSTs de inventario usam CSRF.
- [x] Itens salvos na contagem sao validados contra o inventario enviado.
- [x] Inventario aprovado nao aceita nova contagem.

## Qualidade

- [x] Conflitos de merge removidos do README e da listagem de produtos.
- [x] Runner de testes corrigido.
- [x] Documentacao de testes atualizada.
- [x] Documentacao da Sprint 4 atualizada.
- [x] View de relatorio de limites de produtos integrada ao layout principal.
- [x] Todos os arquivos PHP passaram no lint.
- [x] `tests/run.php` passou com 93 assercoes.

## Comandos de verificacao

```bash
C:\xampp\php\php.exe tests\run.php
```

```bash
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }
```
