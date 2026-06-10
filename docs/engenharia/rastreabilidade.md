# Matriz de Rastreabilidade

Esta matriz liga cada **requisito funcional (RF)** a uma **historia de usuario (US)**, a **sprint** em que foi entregue e ao **teste automatizado (PHPUnit)** que comprova a regra. Ela fecha o ciclo *requisito -> implementacao -> teste*.

> Os testes ficam em `tests/` e rodam com `composer test` (PHPUnit 9.6). Itens marcados como *verificacao manual* sao validados por uso/evidencia (prints), tipicamente fluxos de tela.

| RF | Historia | Sprint | Teste automatizado (PHPUnit) |
| --- | --- | --- | --- |
| RF01 Login | US01 | 2 | `AuthTest::testSenhaComPasswordHashEVerify`, `testSessaoGuardaDadosDoUsuario` |
| RF02 Logout | US02 | 2 | `AuthTest` (sessao) + verificacao manual |
| RF03 Papeis admin/estoquista | US01, US03 | 2 | `PermissaoTest::testAdminEReconhecidoComoAdmin`, `testEstoquistaEReconhecidoComoEstoquista` |
| RF04 Gestao de usuarios | US04 | 2 | verificacao manual |
| RF05 CRUD de produtos | US05, US06, US07 | 1 | `ProdutoApiTest`, `ValidacaoTest::testProdutoComCamposInvalidosDeveRetornarErros`, `testProdutoValidoNaoDeveRetornarErros` |
| RF06 Busca e filtros de produtos | US08 | 1 / 5 | `ValidacaoTest::testPeriodoDeRelatorio` (datas) + verificacao manual |
| RF07 Exclusao restrita a admin | US07 | 1 / 2 | `PermissaoTest` (papel) + verificacao manual |
| RF08 Entradas e saidas | US09, US10 | 1 | `MovimentacaoApiTest::testMovimentacoesValidasNaoRetornamErros`, `testMovimentacaoInvalidaRetornaErrosPorCampo` |
| RF09 Atualizar quantidade | US09, US10 | 1 | verificacao manual |
| RF10 Bloquear saida maior que o saldo | US10 | 1 | `MovimentacaoApiTest` (validacao de movimentacao) |
| RF11 Historico de movimentacoes | US11 | 1 | verificacao manual |
| RF12 Limites minimo e maximo | US12 | 1 | `ValidacaoTest::testProdutoComCamposInvalidosDeveRetornarErros` (max < min) |
| RF13 Alertas de estoque | US13 | 1 | verificacao manual |
| RF14 Abrir inventario | US14 | 4 | verificacao manual |
| RF15 Gravar quantidade na abertura | US14 | 4 | verificacao manual |
| RF16 Registrar contagem | US15 | 4 | `ValidacaoTest::testContagemInvalidaDeveSerRejeitada`, `testContagemValidaDeveSerAceita`; `InventarioTest::testValidarContagem` |
| RF17 Calcular divergencia | US16 | 4 | `InventarioTest::testCalcularDiferenca` |
| RF18 Aprovacao restrita a admin | US17 | 4 | `PermissaoTest` (papel) + verificacao manual |
| RF19 Atualizar estoque e auditar | US17 | 4 | verificacao manual |
| RF20 Relatorio de giro | US18 | 5 | verificacao manual |
| RF21 Relatorio de valorizacao | US19 | 5 | verificacao manual |
| RF22 Movimentacoes por periodo | US20 | 5 | `ValidacaoTest::testPeriodoDeRelatorio` |
| RF23 Dashboard | US21 | 5 | verificacao manual |
| RF24 Exportacao PDF e CSV | US22 | 5 | verificacao manual |
| RF25 Filtros por periodo e categoria | US20, US23 | 5 | `ValidacaoTest::testPeriodoDeRelatorio` |
| RF26 API de produtos | US24 | 3 | `ApiResponseTest`, `ProdutoApiTest`, `ApiProdutosTest` (GET, POST, PUT/PATCH, DELETE, 401, 403) |
| RF27 API de movimentacoes | US25 | 3 | `MovimentacaoApiTest` |

## Resumo da cobertura

- **27 requisitos funcionais** mapeados a historias e sprints.
- **16 requisitos** tem teste automatizado direto no PHPUnit (regras de validacao, calculo, autenticacao, permissao e contratos da API).
- Os demais sao de fluxo de tela (CRUD, telas de inventario, dashboard, exportacao) e validados por **verificacao manual com evidencias** (prints em `docs/sprint-*/evidencias/`).

## Classes de teste (PHPUnit)

| Classe | Cobre |
| --- | --- |
| `tests/AuthTest.php` | Hash de senha, dados de sessao e token CSRF |
| `tests/PermissaoTest.php` | Papeis admin e estoquista |
| `tests/Unit/ValidacaoTest.php` | Validacao de produto, contagem e periodo de relatorio |
| `tests/ApiResponseTest.php` | Formato padrao das respostas JSON |
| `tests/ProdutoApiTest.php` | Contratos e validacoes da API de produtos |
| `tests/MovimentacaoApiTest.php` | Validacao de entrada e saida |
| `tests/InventarioTest.php` | Calculo de divergencia e contagem |
| `tests/Feature/ApiProdutosTest.php` | Cenarios da API (GET, POST, PUT/PATCH, DELETE, 401, 403) |

Total: **25 testes, 97 assercoes** (`composer test`).
