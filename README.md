# Controle de Estoque

Sistema web desenvolvido para gerenciamento de estoque, permitindo o controle de produtos, entradas, saídas, limites de estoque e histórico de movimentações.

O projeto foi desenvolvido em **PHP nativo**, utilizando **HTML**, **CSS**, **MySQL** e o padrão de arquitetura **MVC**.

---

## Sobre o Projeto

O **Controle de Estoque** é uma aplicação web criada com o objetivo de facilitar o gerenciamento de produtos armazenados.

O sistema permite cadastrar produtos, consultar informações do estoque, registrar entradas e saídas, acompanhar movimentações e visualizar alertas quando os produtos estão abaixo do estoque mínimo, no limite mínimo ou acima do estoque máximo.

Inicialmente, o projeto utilizava armazenamento em arquivo JSON. Posteriormente, foi migrado para **MySQL**, tornando a persistência dos dados mais adequada para a proposta do sistema.

---

## Objetivo

O objetivo principal do sistema é oferecer uma solução simples e organizada para controle de estoque.

Entre os objetivos específicos estão:

- Cadastrar e organizar produtos
- Controlar a quantidade disponível em estoque
- Definir estoque mínimo e máximo por produto
- Registrar entradas de estoque
- Registrar saídas de estoque
- Manter histórico de movimentações
- Exibir alertas de reabastecimento
- Facilitar a consulta de produtos por filtros
- Melhorar a visualização dos dados com um front-end mais limpo e profissional

---

## Funcionalidades

### Produtos

- Cadastro de produtos
- Listagem de produtos
- Edição de produtos
- Exclusão de produtos
- Consulta por nome ou código
- Filtros por categoria, unidade e status
- Visualização em tabela
- Visualização em catálogo de cards

### Controle de Estoque

- Registro de entrada de produtos
- Registro de saída de produtos
- Movimentação manual de estoque
- Atualização automática da quantidade disponível
- Validação para evitar saída maior que o estoque disponível

### Estoque Mínimo e Máximo

- Definição de estoque mínimo por produto
- Definição de estoque máximo por produto
- Alerta para produtos abaixo do mínimo
- Alerta para produtos no limite mínimo
- Alerta para produtos acima do máximo

### Movimentações

- Histórico de movimentações por produto
- Registro do tipo de movimentação
- Registro do motivo da movimentação
- Registro da quantidade movimentada
- Registro de observações
- Consulta de entradas e saídas realizadas

### Relatórios

- Total de produtos cadastrados
- Total de unidades em estoque
- Valor estimado do estoque
- Produtos abaixo do estoque mínimo
- Produtos no estoque mínimo
- Produtos acima do estoque máximo
- Últimas movimentações registradas
- Produtos com maior quantidade em estoque

---

## Tecnologias Utilizadas

- PHP 8
- HTML5
- CSS3
- MySQL
- PDO
- XAMPP
- Arquitetura MVC
- Git
- GitHub
- GitFlow

---

## Arquitetura do Projeto

O projeto segue o padrão **MVC**, separando responsabilidades em:

- **Model:** responsável pelo acesso ao banco de dados e regras de persistência
- **View:** responsável pelas telas exibidas ao usuário
- **Controller:** responsável por intermediar as ações entre o usuário, as views e o model

---

## Estrutura de Pastas

```bash
Eng.Software-Controle-de-Estoque/
├── app/
│   ├── Controllers/
│   │   └── ProdutoController.php
│   ├── Models/
│   │   └── ProdutoModel.php
│   └── Views/
│       ├── layouts/
│       │   └── main.php
│       ├── partials/
│       │   ├── sidebar.php
│       │   ├── topbar.php
│       │   └── flash.php
│       └── produtos/
│           ├── listar.php
│           ├── catalogo.php
│           ├── criar.php
│           ├── editar.php
│           ├── entrada.php
│           ├── saida.php
│           ├── movimentar.php
│           ├── historico_movimentacoes.php
│           ├── detalhes_saida.php
│           └── relatorios.php
├── config/
│   └── Database.php
├── database/
│   └── schema.sql
├── public/
│   ├── index.php
│   └── assets/
│       └── css/
│           ├── base.css
│           ├── layout.css
│           ├── components.css
│           └── pages.css
└── README.md
```

---

## Banco de Dados

O sistema utiliza o banco de dados:

```text
controle_estoque
```

As principais tabelas são:

```text
produtos
movimentacoes
```

O script de criação do banco e das tabelas está localizado em:

```bash
database/schema.sql
```

### Tabela `produtos`

Armazena os dados principais dos produtos cadastrados, como:

- Nome
- Código
- Categoria
- Unidade
- Quantidade
- Estoque mínimo
- Estoque máximo
- Preço
- Status
- Descrição

### Tabela `movimentacoes`

Armazena o histórico de entradas e saídas dos produtos, contendo:

- Produto relacionado
- Tipo da movimentação
- Motivo
- Quantidade
- Observação
- Data e hora da movimentação

---

## Rotas Principais

O sistema utiliza o arquivo `public/index.php` como ponto de entrada.

Algumas ações disponíveis são:

```text
index.php?acao=listar
index.php?acao=catalogo
index.php?acao=relatorios
index.php?acao=criar
index.php?acao=editar&id=1
index.php?acao=entrada&id=1
index.php?acao=saida&id=1
index.php?acao=movimentar&id=1
index.php?acao=historico_movimentacoes&id=1
index.php?acao=detalhes_saida&id=1
```

---

## Como Executar o Projeto

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

O caminho final deve ficar assim:

```text
C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

### 4. Iniciar o XAMPP

Abra o **XAMPP Control Panel** e inicie os módulos:

```text
Apache
MySQL
```

### 5. Criar o banco de dados

Abra o phpMyAdmin no navegador:

```text
http://localhost/phpmyadmin
```

Depois execute o script SQL localizado em:

```bash
database/schema.sql
```

Esse script cria o banco `controle_estoque` e as tabelas necessárias para o funcionamento do sistema.

### 6. Configurar a conexão com o banco

Verifique o arquivo:

```bash
config/Database.php
```

Configuração padrão utilizada no XAMPP:

```php
private $host = '127.0.0.1';
private $dbname = 'controle_estoque';
private $user = 'root';
private $pass = '';
private $port = '3306';
```

Caso o MySQL esteja usando outra porta, altere o valor de `$port`.

### 7. Acessar o sistema

No navegador, acesse:

```text
http://localhost/Eng.Software-Controle-de-Estoque/public/
```

---

## Fluxo de Trabalho com GitFlow

O projeto utiliza uma organização baseada em GitFlow.

Branches principais:

```text
main
develop
```

Branches de desenvolvimento:

```text
feature/nome-da-feature
```

Exemplo de criação de uma feature:

```bash
git checkout develop
git pull origin develop
git checkout -b feature/nome-da-feature
```

Exemplo de commit:

```bash
git add .
git commit -m "feat: adiciona nova funcionalidade"
git push -u origin feature/nome-da-feature
```

Após finalizar a feature, deve ser aberto um Pull Request para a branch `develop`.

---

## Atualizações Recentes

Nesta versão, foi realizada uma atualização no front-end do sistema.

As principais melhorias foram:

- Criação de layout base reutilizável
- Criação de sidebar lateral
- Criação de topbar
- Separação de CSS em arquivos organizados
- Atualização da tela principal de estoque
- Criação de catálogo visual de produtos
- Atualização das telas de cadastro e edição
- Atualização das telas de entrada e saída
- Atualização da tela de movimentação manual
- Atualização do histórico de movimentações
- Criação da tela de relatórios
- Padronização visual das telas
- Melhor organização das views com `layouts` e `partials`

---

## Requisitos Implementados

- Cadastro de produtos
- Listagem de produtos
- Edição de produtos
- Exclusão de produtos
- Registro de entrada de estoque
- Registro de saída de estoque
- Histórico de movimentações
- Filtros de busca
- Estoque mínimo por produto
- Estoque máximo por produto
- Alertas de estoque
- Relatórios gerais
- Layout visual atualizado

---

## Informações Acadêmicas

Projeto desenvolvido para fins acadêmicos.

```text
Universidade: Universidade Federal do Tocantins
Curso: Ciência da Computação
Disciplina: Engenharia de Software
Semestre: 2026/1
```

---

## Equipe

- João Pedro Rodrigues Bequiman
- Matheus Sulino Da Silva Costa
- Murillo Fernandes
- Iagor Lourenco
- Giordano Bruno

---

## Observações

- O sistema precisa do Apache e MySQL ativos no XAMPP.
- O banco de dados deve ser criado antes de acessar o sistema.
- O projeto utiliza PHP nativo, sem framework.
- O sistema segue arquitetura MVC.
- As movimentações de estoque são registradas na tabela `movimentacoes`.
- A exclusão de produto remove também suas movimentações por causa do relacionamento com `ON DELETE CASCADE`.

---

## Licença

Este projeto foi desenvolvido para fins acadêmicos.
