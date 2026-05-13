# Sprint 2 - Base de usuarios

## Parte de Joao Pedro Rodrigues Bequiman

Esta feature cria a base inicial de usuarios para a autenticacao do sistema de controle de estoque.

## Entregas realizadas

- Tabela `usuarios` adicionada em `database/schema.sql`.
- Usuarios de teste para os papeis `admin` e `estoquista` adicionados ao script do banco.
- `app/Models/UsuarioModel.php` criado com busca por e-mail, busca por ID e criacao de usuario.
- `app/Views/auth/login.php` criado com formulario de e-mail e senha.
- Rota `index.php?acao=login` adicionada para abrir a tela de login.

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

A validacao do login, criacao de sessao PHP e logout foram implementadas na feature `feature/auth-sessao`.

## Complemento de autenticacao e permissoes

- `app/Controllers/AuthController.php` autentica usuario com `password_verify`.
- `app/Helpers/Sessao.php` centraliza `session_start`, dados do usuario logado e mensagens flash.
- `app/Helpers/Auth.php` centraliza verificacao de login, papel `admin` e papel `estoquista`.
- As rotas internas exigem login.
- As rotas administrativas de produto exigem perfil `admin`.
- O menu e os botoes administrativos sao ocultados para usuarios sem permissao.
