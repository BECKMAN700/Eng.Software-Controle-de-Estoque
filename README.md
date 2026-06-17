# Controle de Estoque

## Universidade Federal do Tocantins (UFT)

**Professor:** Edeilson Milhomem da Silva<br>
**Disciplina:** Engenharia de Software<br>
**Semestre:** 2026.1

**Equipe atual**

- [Joao Pedro Rodrigues Bequiman](https://github.com/BECKMAN700) - repositorio, documentacao, testes e verificacao final
- [Giordano Bruno](https://github.com/GiordanOBru) - relatorios e exportacao
- [Murillo Fernandes de Oliveira](https://github.com/murillofnandes) - dashboard e graficos
- [Matheus Sulino da Silva Costa](https://github.com/vrascode) - filtros avancados e refinamento

> [Iagor Lourenco dos Santos](https://github.com/iagorlrnc) participou de sprints anteriores e nao integra mais a equipe.

---

## Sobre o projeto

O Controle de Estoque e um sistema web em PHP nativo para gerenciar produtos, entradas, saidas, limites de estoque, usuarios, movimentacoes, inventarios e auditoria de ajustes.

A aplicacao segue o padrao MVC, usa MySQL com PDO e possui rotas protegidas por sessao, perfis de usuario e tokens CSRF nos POSTs criticos.

Landing page do produto:

```text
https://beckman700.github.io/Eng.Software-Controle-de-Estoque/
```

Guia de instalacao e configuracao: [INSTALACAO.md](INSTALACAO.md).

Para publicar a landing page no GitHub Pages, use a pasta `docs/` como origem da publicacao nas configuracoes do repositorio.

---

## Funcionalidades

- Cadastro, listagem, edicao e exclusao de produtos
- Registro de entradas e saidas de estoque
- Historico de movimentacoes por produto
- Alertas de estoque minimo e maximo
- Login com sessao PHP
- Perfis `admin` e `estoquista`
- Listagem e cadastro de usuarios por administradores
- API JSON para produtos e movimentacoes
- Modulo de inventario e auditoria da Sprint 4
- Relatorios gerenciais: giro de estoque, valorizacao e movimentacoes por periodo (Sprint 5)
- Exportacao de relatorios em PDF e CSV (Sprint 5)
- Dashboard gerencial com cards e graficos (Sprint 5)
- Filtros avancados por periodo e categoria (Sprint 5)
- Testes automatizados simples em PHP nativo

---

## Sprint 4 - Inventario e Auditoria

Nesta sprint foi implementado o fluxo de inventario do estoque:

1. Abrir inventario com todos os produtos ativos ou por categoria.
2. Salvar a quantidade do sistema no momento da abertura.
3. Registrar contagens fisicas manuais.
4. Calcular divergencias entre sistema e contagem.
5. Aprovar ajustes apenas com usuario admin.
6. Atualizar `produtos.quantidade` apos aprovacao.
7. Registrar auditoria com usuario, produto, quantidade anterior, quantidade nova, diferenca, motivo e data.
8. Consultar a auditoria de inventarios aprovados.

Rotas principais:

- `index.php?acao=inventarios`
- `index.php?acao=inventario_criar`
- `index.php?acao=inventario_detalhar&id=1`
- `index.php?acao=inventario_contagem&id=1`
- `index.php?acao=inventario_divergencias&id=1`
- `index.php?acao=inventario_auditoria&id=1`

---

## Sprint 5 - Relatorios e Dashboard Gerencial

Esta sprint transforma os dados do estoque em informacao gerencial, com relatorios, dashboard, filtros e exportacoes usando dados reais do banco.

**Relatorios:**

- Giro de estoque: produtos mais e menos movimentados, classificados em alto, medio e baixo giro.
- Valorizacao do estoque: valor financeiro por produto (quantidade x preco) e total geral.
- Movimentacoes por periodo: entradas e saidas em um intervalo de datas, com totais.

**Exportacao:**

- Cada relatorio pode ser exportado em PDF (FPDF) e em CSV.

**Dashboard gerencial:**

- Cards de produtos cadastrados, unidades, valor do estoque, produtos criticos, entradas e saidas.
- Graficos (Chart.js): entradas x saidas, produtos mais movimentados e tendencia de movimentacoes.

**Filtros e refinamento:**

- Filtros por periodo e categoria nos produtos e relatorios, com bloqueio de datas invalidas.
- Padronizacao visual de tabelas, botoes e mensagens.

Rotas principais:

- `index.php?acao=dashboard`
- `index.php?acao=relatorios`
- `index.php?acao=giro_estoque`
- `index.php?acao=valorizacao`
- `index.php?acao=movimentacoes_periodo`
- `index.php?acao=exportar-pdf&relatorio=giro_estoque|valorizacao|movimentacoes`
- `index.php?acao=exportar-csv&relatorio=giro_estoque|valorizacao|movimentacoes`

Documentacao detalhada em [docs/sprint-5/](docs/sprint-5/).

---

## Tecnologias

- PHP 8.x
- MySQL
- PDO
- HTML5
- CSS3
- JavaScript
- Chart.js (graficos do dashboard)
- FPDF (exportacao de relatorios em PDF)
- XAMPP
- Git e GitHub com GitFlow

---

## Estrutura principal

```text
Eng.Software-Controle-de-Estoque/
+-- app/
|   +-- Controllers/
|   +-- Helpers/
|   +-- Models/
|   +-- Views/
+-- config/
+-- database/
+-- docs/
+-- public/
|   +-- api/
|   +-- assets/
+-- tests/
+-- README.md
+-- setup.php
```

---

## Banco de dados

O sistema usa o banco:

```text
controle_estoque
```

Tabelas principais:

- `usuarios`
- `produtos`
- `movimentacoes`
- `inventarios`
- `inventario_itens`
- `auditorias_estoque`

O script completo esta em:

```bash
database/schema.sql
```

Usuarios de teste:

| Papel | E-mail | Senha |
| --- | --- | --- |
| admin | `admin@controleestoque.local` | `admin123` |
| estoquista | `estoquista@controleestoque.local` | `estoque123` |

---

## Como executar

Para um passo a passo completo de instalacao, configuracao de banco, execucao e usuarios de teste, consulte [INSTALACAO.md](INSTALACAO.md).

1. Clone o repositorio dentro da pasta `htdocs` do XAMPP.

```bash
git clone https://github.com/BECKMAN700/Eng.Software-Controle-de-Estoque.git
```

2. Acesse a pasta do projeto.

```bash
cd C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

3. Inicie Apache e MySQL no XAMPP.

4. Crie o banco executando o script de setup no navegador:

```text
http://localhost/Eng.Software-Controle-de-Estoque/setup.php
```

   Ou importe manualmente o arquivo `database/schema.sql` pelo phpMyAdmin.

5. Acesse no navegador:

```text
http://localhost/Eng.Software-Controle-de-Estoque/public/
```

---

## API JSON

Endpoints principais:

- `GET index.php?acao=api_produtos`
- `GET index.php?acao=api_produtos&id=1`
- `POST index.php?acao=api_produtos`
- `PUT index.php?acao=api_produtos&id=1`
- `PATCH index.php?acao=api_produtos&id=1`
- `DELETE index.php?acao=api_produtos&id=1`
- `GET index.php?acao=api_movimentacoes`
- `GET index.php?acao=api_movimentacoes&produto_id=1`
- `POST index.php?acao=api_movimentacoes`

Mais detalhes estao em [docs/API.md](docs/API.md).

---

## Testes

Os testes usam **PHPUnit 9.6** (framework de testes do PHP), gerenciado pelo Composer. A aplicacao continua em PHP nativo; o PHPUnit e apenas dependencia de desenvolvimento (`require-dev`).

> Use um PHP com a extensao `mbstring` habilitada (o PHP do XAMPP ja vem com ela). O `composer.phar` esta incluido para quem nao tem o Composer instalado.

Instalar as dependencias de teste (apenas na primeira vez):

```bash
C:\xampp\php\php.exe composer.phar install
```

Rodar todos os testes:

```bash
C:\xampp\php\php.exe composer.phar test
```

Saida detalhada, com cada teste listado:

```bash
C:\xampp\php\php.exe vendor\phpunit\phpunit\phpunit --testdox
```

Cobertura: autenticacao, sessao, permissoes, respostas JSON, validacoes, movimentacoes e regras de inventario (25 testes, 97 assercoes).

---

## Documentacao

Documentacao por sprint:

- Sprint 1 - Produtos e movimentacoes: [planejamento](docs/sprint-1/planejamento-sprint-1.md) | [relatorio](docs/sprint-1/relatorio-sprint-1.md)
- Sprint 2 - Autenticacao e papeis: [planejamento](docs/sprint-2/planejamento-sprint-2.md) | [relatorio](docs/sprint-2/relatorio-sprint-2.md)
- Sprint 3 - API JSON e testes: [planejamento](docs/sprint-3/planejamento-sprint-3.md) | [relatorio](docs/sprint-3/relatorio-sprint-3.md)
- Sprint 4 - Inventario e auditoria: [planejamento](docs/sprint-4/planejamento-sprint-4.md) | [relatorio](docs/sprint-4/relatorio-sprint-4.md)
- Sprint 5 - Relatorios e dashboard: [planejamento](docs/sprint-5/planejamento-sprint-5.md) | [relatorio](docs/sprint-5/relatorio-sprint-5.md)

Documentacao tecnica:

- [docs/API.md](docs/API.md)
- [docs/TESTES.md](docs/TESTES.md)
- [docs/testes-movimentacoes.md](docs/testes-movimentacoes.md)
- [database/schema.sql](database/schema.sql)

---

## GitFlow

Branches principais:

- `main`
- `develop`

Branches de desenvolvimento:

- `feature/nome-da-feature`
- `release/nome-da-release`

Fluxo recomendado:

```bash
git checkout develop
git pull origin develop
git checkout -b feature/nome-da-feature
git add .
git commit -m "feat: descreve a funcionalidade"
git push -u origin feature/nome-da-feature
```

Depois, abrir Pull Request para `develop` e solicitar revisao de outro integrante.

---

## Licenca

Projeto desenvolvido para fins academicos.
