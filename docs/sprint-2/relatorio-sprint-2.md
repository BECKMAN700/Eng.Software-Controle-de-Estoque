# Relatorio - Sprint 2

## Autenticacao, Sessao e Papeis

**Sprint:** 2

## 1. Resumo

A Sprint 2 adicionou a camada de seguranca do sistema: base de usuarios, login com sessao PHP, papeis admin e estoquista e protecao de rotas. A partir dela, o sistema sabe quem esta logado e o que cada perfil pode fazer.

## 2. Entregas realizadas

### Base de usuarios (feature/usuarios-login-view)
- Tabela `usuarios` adicionada em `database/schema.sql`.
- Usuarios de teste para os papeis admin e estoquista.
- `app/Models/UsuarioModel.php` com busca por e-mail, busca por ID, listagem e criacao.
- `app/Views/auth/login.php` com formulario de e-mail e senha.
- Rota `index.php?acao=login`.
- Gerenciamento basico de usuarios para administradores (`UsuarioController`, telas `usuarios/listar.php` e `usuarios/criar.php`).

### Autenticacao e sessao (feature/auth-sessao)
- `app/Controllers/AuthController.php` autentica com `password_verify`.
- `app/Helpers/Sessao.php` centraliza `session_start`, dados do usuario logado e mensagens flash.
- Login cria variaveis de sessao (id, nome, email, papel); logout destroi a sessao.

### Papeis e protecao de rotas (feature/papeis-protecao-rotas)
- `app/Helpers/Auth.php` centraliza verificacao de login, papel admin e papel estoquista.
- Rotas internas exigem login.
- Rotas administrativas de produto e usuarios exigem perfil admin.
- Menu e botoes administrativos ocultos para usuarios sem permissao.

## 3. Usuarios de teste

| Papel | E-mail | Senha |
| --- | --- | --- |
| admin | `admin@controleestoque.local` | `admin123` |
| estoquista | `estoquista@controleestoque.local` | `estoque123` |

As senhas sao apenas para teste local; no banco ficam como hash gerado com `password_hash`.

## 4. Como testar

1. Importar `database/schema.sql` no banco `controle_estoque`.
2. Abrir `public/index.php?acao=login` pelo XAMPP.
3. Entrar com o usuario admin e acessar `index.php?acao=usuarios`.
4. Cadastrar um novo usuario e conferir na listagem.
5. Entrar com o estoquista e conferir que o menu de usuarios nao aparece.

## 5. Evidencias

- Pull Requests #36 a #39 (login, sessao, papeis) e #42 (gerenciamento de usuarios).
- Prints em `docs/sprint-2/evidencias/`.

## 6. Checklist

- [x] Tabela usuarios e usuarios de teste no schema.
- [x] Login com sessao PHP funcionando.
- [x] Logout encerra a sessao.
- [x] Papeis admin e estoquista com permissoes diferentes.
- [x] Rotas internas protegidas por login.
- [x] Acoes criticas restritas a admin.
