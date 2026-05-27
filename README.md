<<<<<<< Updated upstream
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
=======
# 📦 Controle de Estoque

Sistema web desenvolvido para o gerenciamento eficiente de estoque, permitindo o controle de produtos, entradas, saídas e histórico de movimentações de forma prática e organizada.

---

## 📌 Sobre o Projeto

O **Controle de Estoque** é uma aplicação desenvolvida com **PHP nativo**, **HTML**, **CSS** e **MySQL**, seguindo o padrão de arquitetura **MVC**.

O sistema foi criado com o objetivo de auxiliar no gerenciamento de produtos armazenados, possibilitando o cadastro, consulta, atualização e movimentação de itens em estoque.

Inicialmente, o projeto utilizava armazenamento em arquivo JSON, mas foi migrado para **banco de dados MySQL**, tornando a persistência de dados mais adequada e profissional para a proposta do sistema.

---

## 🎯 Objetivo

O principal objetivo do projeto é:

- Facilitar o controle de estoque
- Organizar produtos cadastrados
- Registrar entradas e saídas de mercadorias
- Manter o histórico de movimentações
- Melhorar a visualização e o acompanhamento dos dados

---

## ⚙️ Funcionalidades

### 📋 Produtos
- Cadastro de produtos
- Listagem de produtos
- Edição de produtos
- Exclusão de produtos
- Filtros por nome, categoria, unidade e status

### 📦 Estoque
- Registro de entrada de mercadorias
- Registro de saída de produtos
- Atualização automática da quantidade disponível

### 🕘 Movimentações
- Histórico de movimentações por produto
- Registro do tipo de movimentação
- Registro da quantidade movimentada
- Registro do motivo
- Registro de observações

---

## 🛠️ Tecnologias Utilizadas

- **PHP 8**
- **HTML5**
- **CSS3**
- **MySQL**
- **XAMPP**
- **Arquitetura MVC**
- **Git e GitHub**
- **GitFlow**

---

## 📁 Estrutura do Projeto

```bash
Eng.Software-Controle-de-Estoque/
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── Views/
├── config/
│   └── Database.php
├── database/
│   └── schema.sql
├── public/
│   ├── index.php
│   └── teste_conexao.php
└── README.md
```

---

## 🗄️ Banco de Dados

O sistema utiliza o banco de dados:

```text
controle_estoque
```

Com as tabelas principais:

- `produtos`
- `movimentacoes`

O script de criação do banco e das tabelas está em:

```bash
database/schema.sql
```

---

## 🚀 Como Executar o Projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/BECKMAN700/Eng.Software-Controle-de-Estoque.git
```

### 2. Acessar a pasta do projeto

```bash
cd Eng.Software-Controle-de-Estoque
```

### 3. Colocar o projeto no XAMPP

Copie a pasta do projeto para o diretório:

```text
C:\xampp\htdocs\
```

Ficando assim:

```text
C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

### 4. Iniciar o XAMPP

Abra o **XAMPP Control Panel** e inicie os módulos:

- Apache
- MySQL

### 5. Criar o banco de dados

Abra no navegador:

```text
http://localhost/phpmyadmin
```

Crie um banco chamado:

```text
controle_estoque
```

Depois execute o script do arquivo:

```bash
database/schema.sql
```

### 6. Configurar a conexão com o banco

Verifique o arquivo:

```bash
config/Database.php
```

Exemplo de configuração:

```php
<?php

class Database
{
    private $host = '127.0.0.1';
    private $dbname = 'controle_estoque';
    private $user = 'root';
    private $pass = '';
    private $port = '3306';
    private $conn;

    public function conectar()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->pass
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die('Erro na conexão com o banco: ' . $e->getMessage());
        }
    }
}
```

### 7. Acessar o sistema

No navegador, acesse:

```text
http://localhost/Eng.Software-Controle-de-Estoque/public/
```

---

## 📌 Observações

- O sistema foi migrado de **JSON para MySQL**
- Para funcionamento correto, é necessário que o **Apache** e o **MySQL** estejam ativos no XAMPP
- O banco de dados deve ser criado corretamente antes de executar o projeto
- O projeto foi desenvolvido com fins acadêmicos para a disciplina de **Engenharia de Software**

---

## 👨‍💻 Contato & Créditos

Projeto acadêmico colaborativo — **UFT (2026/1)**

### 👥 Equipe

- João Pedro Rodrigues Bequiman
- Matheus Sulino Da Silva Costa
- Murillo Fernandes
- Iagor Lourenco
- Giordano Bruno

---

## 📄 Licença

Este projeto pode ser utilizado para fins acadêmicos.

## Sprint 4 — Relatórios, Auditoria e Testes

### Funcionalidades
- Relatório de divergências de estoque
- Auditoria das alterações da Sprint 4
- Controle de estoque mínimo e máximo
- Testes de movimentações e inventário

### Relatório de divergências
Acesse:

index.php?acao=divergencias

O relatório identifica:
- produtos abaixo do estoque mínimo
- produtos acima do estoque máximo

### Documentação
Arquivos disponíveis:
- docs/sprint4.md
- docs/auditoria-sprint4.md
- tests/testes-movimentacoes.md