# Planejamento - Sprint 2

## Autenticacao, Sessao e Papeis

**Disciplina:** Engenharia de Software - UFT
**Periodo:** Maio de 2026
**Sprint:** 2
**Guia base:** Guia de trabalho das Sprints 2 e 3

## 1. Objetivo

Adicionar autenticacao ao sistema: base de usuarios, login com sessao PHP e papeis (admin e estoquista) com protecao de rotas. Esta sprint garante que apenas usuarios autenticados usem o sistema e que acoes criticas fiquem restritas ao administrador.

## 2. Escopo

- Tabela de usuarios e base de cadastro.
- Tela de login e UsuarioModel.
- AuthController com login, autenticar e logout.
- Sessao PHP centralizada (id, nome, email, papel).
- Papeis admin e estoquista.
- Protecao de rotas e ocultacao de acoes para quem nao e admin.

## 3. Divisao de responsabilidades

| Integrante | Branch | Responsabilidade |
| --- | --- | --- |
| Joao Pedro | feature/usuarios-login-view | Base de usuarios, banco e tela de login |
| Giordano Bruno | feature/auth-sessao | AuthController, login, logout e sessao PHP |
| Murillo Fernandes | feature/papeis-protecao-rotas | Papeis, permissoes e protecao de rotas |

## 4. Criterios de aceite

- Login com e-mail e senha funciona; senha errada nao entra.
- Senhas ficam como hash (password_hash), nunca em texto puro.
- Logout encerra a sessao.
- Rotas internas sao bloqueadas para quem nao esta logado.
- Admin e estoquista tem permissoes diferentes.
- O menu nao mostra opcoes proibidas para o estoquista.

## 5. Regra de trabalho

Cada integrante cria a branch a partir da develop, abre PR para develop e pede revisao de outro integrante. Ninguem aprova nem faz merge do proprio PR.
