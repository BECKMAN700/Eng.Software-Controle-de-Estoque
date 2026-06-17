# Instalacao e configuracao

Este guia mostra como instalar e rodar o sistema Controle de Estoque em ambiente local usando XAMPP, PHP e MySQL.

## Requisitos

- Windows com XAMPP instalado.
- PHP 8.0 ou superior.
- Apache e MySQL ativos pelo painel do XAMPP.
- Git instalado para clonar o repositorio.
- Navegador atualizado.

## 1. Instalar o XAMPP

1. Baixe o XAMPP em <https://www.apachefriends.org/pt_br/index.html>.
2. Instale usando as opcoes padrao.
3. Abra o painel do XAMPP.
4. Inicie os servicos `Apache` e `MySQL`.

O projeto foi pensado para ficar dentro da pasta:

```text
C:\xampp\htdocs
```

## 2. Clonar o projeto

Abra o terminal na pasta `htdocs`:

```bash
cd C:\xampp\htdocs
```

Clone o repositorio:

```bash
git clone https://github.com/BECKMAN700/Eng.Software-Controle-de-Estoque.git
```

Acesse a pasta do projeto:

```bash
cd C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

## 3. Importar o banco MySQL

O banco usado pelo sistema se chama:

```text
controle_estoque
```

### Opcao A: setup automatico

Com Apache e MySQL ativos, acesse no navegador:

```text
http://localhost/Eng.Software-Controle-de-Estoque/setup.php
```

Esse script cria o banco, tabelas e usuarios iniciais com base em `database/schema.sql`.

### Opcao B: importacao manual pelo phpMyAdmin

1. Acesse:

```text
http://localhost/phpmyadmin
```

2. Clique em `Importar`.
3. Selecione o arquivo:

```text
database/schema.sql
```

4. Confirme a importacao.

O script ja contem o comando `CREATE DATABASE IF NOT EXISTS controle_estoque`, entao nao e necessario criar o banco manualmente antes.

## 4. Configurar a conexao

Por padrao, a conexao esta configurada para o MySQL local do XAMPP:

```text
Host: 127.0.0.1
Banco: controle_estoque
Usuario: root
Senha: vazia
Porta: 3306
```

Essas configuracoes ficam em `config/Database.php`.

Se precisar alterar sem editar o arquivo, defina variaveis de ambiente:

```text
DB_HOST
DB_NAME
DB_USER
DB_PASS
DB_PORT
```

## 5. Rodar o projeto

Com Apache e MySQL ativos, acesse:

```text
http://localhost/Eng.Software-Controle-de-Estoque/public/
```

Se a tela de login aparecer, a instalacao principal esta funcionando.

## 6. Usuario e senha de teste

| Papel | E-mail | Senha |
| --- | --- | --- |
| Admin | `admin@controleestoque.local` | `admin123` |
| Estoquista | `estoquista@controleestoque.local` | `estoque123` |

Use o usuario `admin` para acessar todas as funcionalidades, incluindo usuarios, inventarios, relatorios e dashboard.

## 7. Testes automatizados

Instale as dependencias de desenvolvimento:

```bash
C:\xampp\php\php.exe composer.phar install
```

Rode todos os testes:

```bash
C:\xampp\php\php.exe composer.phar test
```

Saida esperada:

```text
OK (25 tests, 97 assertions)
```

## Solucao de problemas

### Erro de conexao com o banco

Verifique se o MySQL esta ativo no XAMPP e se o banco `controle_estoque` foi criado corretamente.

### Pagina nao encontrada

Confirme se o projeto esta em:

```text
C:\xampp\htdocs\Eng.Software-Controle-de-Estoque
```

E acesse pela URL:

```text
http://localhost/Eng.Software-Controle-de-Estoque/public/
```

### Login nao funciona

Reimporte o arquivo `database/schema.sql` ou execute novamente o `setup.php` para recriar os usuarios de teste.

