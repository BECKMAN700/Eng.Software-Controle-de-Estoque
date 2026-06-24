# Contribuições de Matheus Sulino

## Resumo geral

Este documento consolida as contribuições atribuídas a Matheus Sulino no histórico Git do projeto, considerando os autores informados para a análise:

- `vras`
- `Matheus Sulino`
- `matheus`

A busca foi realizada em todo o histórico disponível do repositório, incluindo branches locais e remotas, por meio de `git log --all`. Também foi feita uma verificação específica na branch `develop` para identificar quais commits foram aceitos ou integrados ao fluxo principal de desenvolvimento.

Foram encontrados 23 commits relacionados aos autores analisados. Todos os 23 commits também aparecem no histórico da branch `develop`, o que indica que as alterações foram integradas ou estão presentes na linha de desenvolvimento do projeto.

## Total de commits encontrados

| Métrica | Total |
| --- | ---: |
| Commits encontrados em todas as branches | 23 |
| Commits encontrados na branch `develop` | 23 |
| Commits considerados integrados à `develop` | 23 |

## Total por autor/nome usado no Git

| Autor no Git | Total de commits |
| --- | ---: |
| `vras` | 11 |
| `Matheus Sulino` | 6 |
| `matheus` | 6 |
| **Total geral** | **23** |

## Observações sobre autores diferentes no Git

O histórico registra contribuições com três nomes de autor diferentes. Os commits de `Matheus Sulino` e `matheus` usam o mesmo e-mail, `matheussulino12@gmail.com`, o que indica forte relação entre essas duas identidades Git.

O autor `vras` aparece com o e-mail `82063413+vrascode@users.noreply.github.com`. Como a solicitação desta análise determinou explicitamente que `vras` deve ser considerado como pertencente ao mesmo conjunto de contribuições, os commits desse autor foram incluídos no relatório.

É importante observar que parte dos commits de `vras` são commits de merge de pull requests. Esses commits evidenciam participação no processo de integração de funcionalidades ao projeto, ainda que o conteúdo original de algumas alterações possa ter sido desenvolvido em branches de outros autores.

## Lista de commits encontrados

| Hash | Data | Autor | Branch/referência provável | Presente na `develop` | Mensagem |
| --- | --- | --- | --- | --- | --- |
| `65cf42f` | 2026-06-23 | Matheus Sulino | `origin/feature/sidebar-recolhivel` | Sim | feat: adiciona sidebar recolhivel com persistencia |
| `c56bd71` | 2026-06-23 | Matheus Sulino | `feature/melhorias-login` | Sim | fix: melhora mensagens de validacao do login |
| `980b992` | 2026-06-23 | Matheus Sulino | `feature/melhorias-login~1` | Sim | feat: adiciona mostrar e ocultar senha no login |
| `8982385` | 2026-06-23 | Matheus Sulino | `feature/melhorias-login~2` | Sim | feat: adiciona opcao lembrar email no login |
| `0a13162` | 2026-06-23 | Matheus Sulino | `feature/revisao-cadastro-usuario` | Sim | docs: adiciona revisao e checklist do cadastro de usuario |
| `6ff9709` | 2026-06-23 | Matheus Sulino | `feature/docs-testes-verificacao` | Sim | feat: adiciona cadastro de usuario |
| `40df9aa` | 2026-06-08 | matheus | `origin/feature/filtros-refinamento` | Sim | feat: adiciona filtros avancados e refinamento do produto |
| `318c508` | 2026-06-08 | vras | `origin/feature/filtros-refinamento~1` | Sim | Merge pull request #52 from BECKMAN700/feature/dashboard-gerencial |
| `4566e37` | 2026-05-26 | matheus | `origin/feature/sprint4-relatorioauditoria-docs` | Sim | feat: adiciona relatório de divergências, auditoria e documentação da sprint 4 |
| `c266e0e` | 2026-05-26 | matheus | `origin/feature/sprint4-relatorioauditoria-docs~1` | Sim | resolve conflitos e adiciona sprint 4 |
| `beb5bdb` | 2026-05-26 | vras | `origin/feature/sprint4-relatorioauditoria-docs~2` | Sim | Merge pull request #47 from BECKMAN700/feature/sprint4-contagem-aprovacao |
| `138d8fe` | 2026-05-12 | vras | `origin/feature/sprint4-base-inventario~1^2` | Sim | atualiza README com documentação do projeto |
| `86168c4` | 2026-05-09 | vras | `origin/feature/testes-validacao-api~1` | Sim | Merge pull request #40 from BECKMAN700/feature/api-php-nativo |
| `3b2f38d` | 2026-04-29 | vras | `origin/feature/sprint4-base-inventario~1^2~2` | Sim | Merge pull request #35 from BECKMAN700/develop |
| `413d891` | 2026-04-29 | matheus | `origin/feature/atualizacao-do-front01` | Sim | fix: corrige estilos e padroniza telas do novo front |
| `be40d55` | 2026-04-21 | matheus | `origin/feature/estoque-minimo-maximo` | Sim | feat: define estoque mínimo e máximo por produto |
| `06f9507` | 2026-04-21 | vras | `origin/feature/estoque-minimo-maximo~1` | Sim | Merge pull request #28 from BECKMAN700/feature/reabastecimento-ajuste |
| `5b3c515` | 2026-03-25 | vras | `origin/feature/saida-estoque~4` | Sim | Revise README with project details and structure |
| `ceb5c54` | 2026-03-25 | vras | `origin/feature-de-ajustes-para-main~2` | Sim | Merge pull request #8 from BECKMAN700/feature/excluir-produto-correcao |
| `388e689` | 2026-03-24 | vras | `origin/revert-5-revert-4-feature/excluir-produto~1` | Sim | Merge pull request #5 from BECKMAN700/revert-4-feature/excluir-produto |
| `f84d57e` | 2026-03-24 | vras | `origin/revert-4-feature/excluir-produto~1` | Sim | Merge pull request #4 from BECKMAN700/feature/excluir-produto |
| `b725c62` | 2026-03-24 | matheus | `origin/feature/editar-produto` | Sim | feat: adiciona edição de produtos |
| `4259e0f` | 2026-03-24 | vras | `origin/revert-4-feature/excluir-produto~2` | Sim | Update student list in README.md |

## Commits separados por área/funcionalidade

### Autenticação, cadastro e experiência de login

- `6ff9709` - adiciona cadastro de usuário, com alterações em controller, model, view e rota.
- `c56bd71` - melhora mensagens de validação do login.
- `980b992` - adiciona recurso para mostrar e ocultar senha no login.
- `8982385` - adiciona opção de lembrar e-mail no login.
- `0a13162` - documenta revisão e checklist do cadastro de usuário.

### Produtos, estoque e regras de negócio

- `b725c62` - adiciona edição de produtos.
- `be40d55` - define estoque mínimo e máximo por produto, incluindo alteração em `database/schema.sql`.
- `06f9507` - integra ajustes de reabastecimento.
- `f84d57e` - integra funcionalidade de exclusão de produto.
- `388e689` - integra reversão relacionada à exclusão de produto.
- `ceb5c54` - integra correção da funcionalidade de exclusão de produto.
- `40df9aa` - adiciona filtros avançados e refinamento de produtos.
- `c266e0e` - resolve conflitos e adiciona itens da Sprint 4 relacionados a produtos e divergências.

### Relatórios, auditoria e divergências

- `40df9aa` - ajusta relatórios de giro de estoque, movimentações por período e valorização.
- `4566e37` - adiciona documentação de relatório de divergências, auditoria e testes de movimentações.
- `c266e0e` - adiciona view de divergências e atualizações associadas à Sprint 4.

### Inventário

- `beb5bdb` - integra contagem, divergências e aprovação de inventário por meio de merge de pull request.
- `138d8fe` - atualiza documentação do projeto no README em contexto posterior à evolução das funcionalidades.

### Dashboard e monitoramento gerencial

- `318c508` - integra a feature de dashboard gerencial, com controller, model, view, sidebar e rota.

### Interface, layout e padronização visual

- `413d891` - corrige estilos e padroniza telas do novo front-end.
- `65cf42f` - adiciona sidebar recolhível com persistência.
- `3b2f38d` - integra amplo conjunto de alterações de layout, telas de produto, componentes, CSS e estrutura visual.

### API, documentação e estrutura do projeto

- `86168c4` - integra API PHP nativa, ajustes em README, controllers, models, endpoints públicos, setup e documentação da Sprint 3.
- `5b3c515` - revisa README com detalhes e estrutura do projeto.
- `4259e0f` - atualiza lista de estudantes no README.
- `0a13162` - adiciona checklist e revisão de cadastro de usuário.
- `4566e37` - adiciona documentação de auditoria e testes de movimentações.

## Funcionalidades desenvolvidas

As principais funcionalidades associadas aos commits analisados são:

- Cadastro de usuário.
- Melhorias de login, incluindo lembrar e-mail e mostrar/ocultar senha.
- Edição de produtos.
- Controle de estoque mínimo e máximo por produto.
- Filtros avançados e refinamento da listagem de produtos.
- Relatórios de giro de estoque, movimentações por período e valorização.
- Documentação de auditoria, divergências e testes de movimentações.
- Sidebar recolhível com persistência.
- Integração de dashboard gerencial.
- Integração de APIs em PHP nativo.
- Atualização e organização da documentação do projeto.

## Correções realizadas

Entre as correções identificadas no histórico, destacam-se:

- Melhoria das mensagens de validação do login.
- Correção e padronização de estilos do novo front-end.
- Resolução de conflitos relacionados à Sprint 4.
- Integração de correções da funcionalidade de exclusão de produto.
- Integração de ajustes de reabastecimento.
- Reversões e correções associadas ao fluxo de exclusão de produto.

## Evidências de commits presentes na develop

A análise específica da branch `develop` retornou os mesmos 23 commits encontrados em `--all`. Além disso, a verificação por ancestralidade confirmou que todos os commits filtrados são ancestrais de `develop`.

Comando utilizado para listar commits desses autores em `develop`:

```bash
git log develop --regexp-ignore-case --author='vras\|Matheus Sulino\|matheus' --format='%H %h %an <%ae> %ad %s' --date=short
```

Resultado consolidado:

| Situação | Total |
| --- | ---: |
| Commits analisados | 23 |
| Commits presentes em `develop` | 23 |
| Commits não encontrados em `develop` | 0 |

Também foi utilizada a verificação por commit:

```bash
git merge-base --is-ancestor <hash-do-commit> develop
```

Para todos os commits listados neste documento, o comando retornou sucesso, indicando presença na linha histórica da branch `develop`.

## Comandos executados

Os seguintes comandos Git foram utilizados para validar as informações deste relatório:

```bash
git shortlog -sn --all
git log --all --regexp-ignore-case --author='vras\|Matheus Sulino\|matheus' --format='%H%x09%h%x09%an%x09%ae%x09%ad%x09%D%x09%s' --date=short
git log develop --regexp-ignore-case --author='vras\|Matheus Sulino\|matheus' --format='%H%x09%h%x09%an%x09%ae%x09%ad%x09%D%x09%s' --date=short
git name-rev --name-only --refs='refs/heads/*' --refs='refs/remotes/origin/*' <hash-do-commit>
git merge-base --is-ancestor <hash-do-commit> develop
git show --format='' --name-only <hash-do-commit>
git show --format='' --numstat <hash-do-commit>
git status --short
```

## Conclusão final

Com base no histórico Git real do repositório, foram encontrados 23 commits relacionados aos autores `vras`, `Matheus Sulino` e `matheus`. Esses commits abrangem funcionalidades relevantes do sistema, como cadastro de usuário, login, edição de produtos, controle de estoque mínimo e máximo, filtros avançados, relatórios, documentação, dashboard, API, inventário e melhorias de interface.

Todos os commits identificados estão presentes na branch `develop`, o que reforça que as contribuições analisadas foram integradas ao fluxo principal de desenvolvimento. A existência de diferentes nomes de autor no Git foi documentada de forma transparente, respeitando o critério solicitado para considerar essas identidades como vinculadas às contribuições de Matheus Sulino.
