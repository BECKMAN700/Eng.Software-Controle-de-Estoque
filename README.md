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

---

## 🗄️ Banco de Dados

O sistema utiliza o banco de dados:

controle_estoque

Com as tabelas principais:

produtos
movimentacoes

O script de criação do banco e das tabelas está em:

database/schema.sql

---

## 🚀 Como Executar o Projeto

1. Clonar o repositório
git clone https://github.com/BECKMAN700/Eng.Software-Controle-de-Estoque.git

2. Acessar a pasta do projeto
cd Eng.Software-Controle-de-Estoque

3. Colocar o projeto no XAMPP

Copie a pasta do projeto para o diretório:

C:\xampp\htdocs\

Ficando assim:

C:\xampp\htdocs\Eng.Software-Controle-de-Estoque

4. Iniciar o XAMPP

Abra o XAMPP Control Panel e inicie os módulos:

Apache
MySQL

5. Criar o banco de dados

Abra no navegador:

http://localhost/phpmyadmin

Crie um banco chamado:

controle_estoque

Depois execute o script do arquivo:

database/schema.sql

6. Configurar a conexão com o banco

Verifique o arquivo:

config/Database.php

Exemplo de configuração:

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
7. Acessar o sistema

No navegador, acesse:

http://localhost/Eng.Software-Controle-de-Estoque/public/

---

## 📄 Licença

Este projeto pode ser utilizado para fins acadêmicos.

---


## 📌 Observações
O sistema foi migrado de JSON para MySQL
Para funcionamento correto, é necessário que o Apache e o MySQL estejam ativos no XAMPP
O banco de dados deve ser criado corretamente antes de executar o projeto
O projeto foi desenvolvido com fins acadêmicos para a disciplina de Engenharia de Software
---

## 👨‍💻 Contato & Créditos

Projeto acadêmico colaborativo — UFT (2026/1)

### 👥 Equipe

* João Pedro Rodrigues Bequiman - feature/cadastro-produto
* Matheus Sulino Da Silva Costa - feature/editar-produto
* Murillo Fernandes - feature/listagem-produtos
* Iagor Lourenco - feature/excluir-produto
* Giordano Bruno - feature/movimentacao-estoque
