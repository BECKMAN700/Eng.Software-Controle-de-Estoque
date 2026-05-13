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

## Links uteis

<small>
<a href="docs/SPRINT2.md">Documentacao da Sprint 2</a><br>
<a href="docs/SPRINT3.md">Documentacao da Sprint 3</a><br>
<a href="docs/API.md">Documentacao da API</a><br>
<a href="docs/TESTES.md">Como rodar os testes</a><br>
<a href="database/schema.sql">Script do banco de dados</a>
</small>

---

## Sobre o projeto

O **Controle de Estoque** e um sistema web desenvolvido em PHP nativo para gerenciar produtos, entradas, saidas, limites de estoque, historico de movimentacoes e relatorios.

A aplicacao permite cadastrar produtos, acompanhar quantidade disponivel, controlar estoque minimo e maximo, registrar movimentacoes, gerenciar usuarios administradores/estoquistas e expor dados por uma API JSON em PHP nativo.

Entre as principais funcionalidades estao:

- Cadastro, listagem, edicao e exclusao de produtos
- Registro de entradas e saidas de estoque
- Historico de movimentacoes por produto
- Alertas de estoque minimo, limite minimo e estoque maximo
- Login com sessao PHP
- Perfis `admin` e `estoquista`
- Listagem e cadastro de usuarios por administradores
- Protecao de rotas internas e administrativas
- API JSON para produtos e movimentacoes
- Validacoes reutilizaveis e testes simples em PHP

---

## Objetivo

Desenvolver um sistema web simples e organizado para controle de estoque, aplicando conceitos de MVC, persistencia em MySQL, autenticacao, autorizacao por papeis, gerenciamento basico de usuarios, API em PHP nativo, validacoes, testes e fluxo GitFlow.

---

## Tecnologias utilizadas

- **Linguagem:** PHP 8.x
- **Banco de dados:** MySQL
- **Interface:** HTML5 e CSS3
- **Persistencia:** PDO
- **Servidor local:** XAMPP
- **Arquitetura:** MVC
- **Versionamento:** Git e GitHub com GitFlow

---

## Estrutura principal

```text
Eng.Software-Controle-de-Estoque/
├── app/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Models/
│   └── Views/
├── config/
├── database/
├── docs/
├── public/
│   ├── api/
│   └── assets/
├── tests/
├── README.md
└── setup.php
```

---

## Banco de dados

O sistema utiliza o banco:

```text
controle_estoque
```

Tabelas principais:

- `usuarios`
- `produtos`
- `movimentacoes`

O script completo esta em:

```bash
database/schema.sql
```

Usuarios de teste:

| Papel | E-mail | Senha |
| --- | --- | --- |
| admin | `admin@controleestoque.local` | `admin123` |
| estoquista | `estoquista@controleestoque.local` | `estoque123` |

As senhas ficam armazenadas como hash no banco.

---

## Rotas principais

- `index.php?acao=listar`
- `index.php?acao=catalogo`
- `index.php?acao=relatorios`
- `index.php?acao=criar`
- `index.php?acao=usuarios`
- `index.php?acao=usuario_criar`
- `index.php?acao=api_produtos`
- `index.php?acao=api_movimentacoes`

---

## API JSON

As APIs usam `public/index.php` como ponto de entrada e tambem possuem atalhos em `public/api`.

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

As respostas seguem o padrao:

```json
{
  "erro": false,
  "mensagem": "Mensagem da operacao",
  "dados": []
}
```

Mais detalhes estao em [docs/API.md](docs/API.md).

---

## Como rodar o projeto localmente

1. Clone o repositorio dentro da pasta `htdocs` do XAMPP:

```bash
git clone https://github.com/BECKMAN700/Eng.Software-Controle-de-Estoque.git
```

2. Acesse a pasta do projeto:

```bash
cd C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

3. Inicie o Apache e o MySQL no XAMPP.

4. Crie o banco pelo phpMyAdmin ou execute:

```text
database/schema.sql
```

5. Verifique a conexao em:

```text
config/Database.php
```

Configuracao padrao:

```php
private $host = '127.0.0.1';
private $dbname = 'controle_estoque';
private $user = 'root';
private $pass = '';
private $port = '3306';
```

6. Acesse no navegador:

```text
http://localhost/Eng.Software-Controle-de-Estoque/public/
```

---

## Como rodar os testes

Os testes ficam na pasta `tests` e nao exigem Composer.

```bash
C:\xampp\php\php.exe tests\run_tests.php
```

Saida esperada:

```text
Todos os testes passaram. Total de assercoes: 27
```

Mais detalhes estao em [docs/TESTES.md](docs/TESTES.md).

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

## Status das Sprints 2 e 3

- Base de usuarios, banco e tela de login implementados
- Gerenciamento basico de usuarios por administradores implementado
- Autenticacao, logout e sessao PHP implementados
- Papeis, permissoes e protecao de rotas implementados
- API em PHP nativo para produtos e movimentacoes implementada
- Respostas JSON padronizadas com helper
- Validacoes de produto e movimentacao centralizadas
- Testes PHP simples adicionados
- Documentacao tecnica atualizada

---

## Licenca

Projeto desenvolvido para fins academicos.
