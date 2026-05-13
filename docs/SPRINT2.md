# Sprint 2 - Base de usuarios

## Parte de Joao Pedro Rodrigues Bequiman

Esta feature cria a base inicial de usuarios para a autenticacao do sistema de controle de estoque.

## Entregas realizadas

- Tabela `usuarios` adicionada em `database/schema.sql`.
- Usuarios de teste para os papeis `admin` e `estoquista` adicionados ao script do banco.
- `app/Models/UsuarioModel.php` criado com busca por e-mail, busca por ID e criacao de usuario.
- `app/Views/auth/login.php` criado com formulario de e-mail e senha.
- Rota `index.php?acao=login` adicionada para abrir a tela de login.
- Gerenciamento basico de usuarios adicionado para administradores.
- Tela `app/Views/usuarios/listar.php` criada para listar usuarios cadastrados.
- Tela `app/Views/usuarios/criar.php` criada para cadastrar novos usuarios.
- `app/Controllers/UsuarioController.php` criado para controlar listagem e cadastro.

## Usuarios de teste

| Papel | E-mail | Senha |
| --- | --- | --- |
| admin | `admin@controleestoque.local` | `admin123` |
| estoquista | `estoquista@controleestoque.local` | `estoque123` |

As senhas acima sao apenas para teste local. No banco, elas ficam armazenadas como hash gerado com `password_hash`, nao em texto puro.

## Como testar esta parte

1. Importar novamente o arquivo `database/schema.sql` no banco `controle_estoque`.
2. Abrir `public/index.php?acao=login` pelo XAMPP.
3. Conferir se a tela de login aparece com os campos de e-mail e senha.
4. Conferir no banco se os usuarios `admin` e `estoquista` foram criados.
5. Entrar com usuario `admin`.
6. Acessar `index.php?acao=usuarios`.
7. Cadastrar um novo usuario e conferir se ele aparece na listagem.

A validacao do login, criacao de sessao PHP e logout ficam na feature `feature/auth-sessao`.
