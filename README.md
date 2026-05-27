# Controle de Estoque

## Universidade Federal do Tocantins (UFT)

**Professor:** Edeilson Milhomem da Silva<br>
**Disciplina:** Engenharia de Software<br>
**Semestre:** 2026.1

**Equipe**

- [Joao Pedro Rodrigues Bequiman](https://github.com/BECKMAN700)
- [Giordano Bruno](https://github.com/GiordanOBru)
- [Murillo Fernandes de Oliveira](https://github.com/murillofnandes)
- [Iagor Lourenco dos Santos](https://github.com/iagorlrnc)
- [Matheus Sulino da Silva Costa](https://github.com/vrascode)

---

## Sobre o projeto

O Controle de Estoque e um sistema web em PHP nativo para gerenciar produtos, entradas, saidas, limites de estoque, usuarios, movimentacoes, inventarios e auditoria de ajustes.

A aplicacao segue o padrao MVC, usa MySQL com PDO e possui rotas protegidas por sessao, perfis de usuario e tokens CSRF nos POSTs criticos.

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

## Tecnologias

- PHP 8.x
- MySQL
- PDO
- HTML5
- CSS3
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

1. Clone o repositorio dentro da pasta `htdocs` do XAMPP.

```bash
git clone https://github.com/BECKMAN700/Eng.Software-Controle-de-Estoque.git
```

2. Acesse a pasta do projeto.

```bash
cd C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

3. Inicie Apache e MySQL no XAMPP.

4. Crie o banco pelo phpMyAdmin ou execute o script:

```text
database/schema.sql
```

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

Os testes ficam na pasta `tests` e nao exigem Composer.

```bash
C:\xampp\php\php.exe tests\run.php
```

Ou:

```bash
C:\xampp\php\php.exe tests\run_tests.php
```

Os testes cobrem autenticacao, permissoes, respostas JSON, validacoes, movimentacoes e regras de inventario.

---

## Documentacao

- [docs/API.md](docs/API.md)
- [docs/TESTES.md](docs/TESTES.md)
- [docs/auditoria-sprint4.md](docs/auditoria-sprint4.md)
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
