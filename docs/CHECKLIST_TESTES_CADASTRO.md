# Checklist de Testes: Funcionalidade de Cadastro de Usuário

**Feature:** Cadastro de Usuário  
**Commit:** 6ff9709 — feat: adiciona cadastro de usuario  
**Data:** 2026-06-23  
**Versão do Teste:** 1.0  

---

## 1. Testes de Acesso e Navegação

| ID | Descrição | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| NAV-001 | Acessar página de cadastro (rota pública) | Digitar URL: `http://localhost/Eng.Software-Controle-de-Estoque/public/index.php?acao=cadastro` | Exibir formulário de cadastro com campos: Nome, E-mail, Senha, Confirmar Senha | ⏳ |
| NAV-002 | Link "Cadastre-se" na página de login | Na página de login, clicar em "Cadastre-se" | Redirecionar para página de cadastro | ⏳ |
| NAV-003 | Link "Voltar ao login" na página de cadastro | Na página de cadastro, clicar em "Voltar ao login" | Redirecionar para página de login | ⏳ |
| NAV-004 | Acesso a cadastro quando já logado | Fazer login → Acessar `?acao=cadastro` | Redirecionar para dashboard/listar | ⏳ |

---

## 2. Testes de Validação de Formulário

### 2.1 Campos Obrigatórios

| ID | Campo | Entrada | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| VAL-001 | Nome | (vazio) | Mensagem de erro: "Preencha todos os campos." | ⏳ |
| VAL-002 | E-mail | (vazio) | Mensagem de erro: "Preencha todos os campos." | ⏳ |
| VAL-003 | Senha | (vazio) | Mensagem de erro: "Preencha todos os campos." | ⏳ |
| VAL-004 | Confirmar Senha | (vazio) | Mensagem de erro: "Preencha todos os campos." | ⏳ |

### 2.2 Validação de E-mail

| ID | Campo | Entrada | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| EMAIL-001 | E-mail | `usuario` (sem @) | Mensagem de erro: "E-mail inválido." | ⏳ |
| EMAIL-002 | E-mail | `usuario@` (sem domínio) | Mensagem de erro: "E-mail inválido." | ⏳ |
| EMAIL-003 | E-mail | `usuario@exemplo` (sem TLD) | Mensagem de erro: "E-mail inválido." | ⏳ |
| EMAIL-004 | E-mail | `usuario@exemplo.com` (válido) | Passar para próximas validações | ⏳ |
| EMAIL-005 | E-mail | `admin@controleestoque.local` (duplicado) | Mensagem de erro: "E-mail já cadastrado. Faça login ou recupere sua senha." | ⏳ |
| EMAIL-006 | E-mail | `novo@exemplo.com` (novo) | Passar para próximas validações | ⏳ |

### 2.3 Validação de Senha

| ID | Campo | Entrada | Confirmação | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| SENHA-001 | Senha | `abc123` | `abc123` | ✅ Senhas coincidem | ⏳ |
| SENHA-002 | Senha | `senha123` | `senha456` | Mensagem de erro: "A senha e a confirmação não coincidem." | ⏳ |
| SENHA-003 | Senha | (espaços) | (mesmos espaços) | Mensagem de erro: "A senha e a confirmação não coincidem." (diferentes) | ⏳ |
| SENHA-004 | Senha | `12345678` | `12345678` | ✅ Senhas coincidem (aceita sem validação de força) | ⏳ |

---

## 3. Testes de Funcionalidade de Cadastro

### 3.1 Cadastro Bem-Sucedido

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| CAD-001 | Cadastro válido com dados completos | Nome: "João Silva", E-mail: "joao@exemplo.com", Senha: "senha123", Confirmar: "senha123" | Sucesso: Redirecionar para login com mensagem "Cadastro realizado com sucesso. Faça login." | ⏳ |
| CAD-002 | Cadastro com nome com espaços e acentos | Nome: "José da Silva Pereira", E-mail: "jose@exemplo.com", Senha: "abc12345", Confirmar: "abc12345" | Sucesso: Usuário criado com nome preservado (acentos OK) | ⏳ |
| CAD-003 | Cadastro com e-mail em maiúsculas | E-mail: "USUARIO@EXEMPLO.COM" | Sucesso: E-mail armazenado em minúsculas (ou preserva conforme entrada) | ⏳ |
| CAD-004 | Cadastro com múltiplos usuários sequenciais | Repetir CAD-001 com e-mails diferentes | Todos os usuários cadastrados com sucesso | ⏳ |

### 3.2 Cadastro com Erro

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| CAD-ERR-001 | E-mail já cadastrado | Usar e-mail: `admin@controleestoque.local` (do seed) | Mensagem de erro: "E-mail já cadastrado. Faça login ou recupere sua senha." | ⏳ |
| CAD-ERR-002 | Senhas não coincidem | Senha: "abc123", Confirmar: "abc124" | Mensagem de erro: "A senha e a confirmação não coincidem." | ⏳ |
| CAD-ERR-003 | Erro ao inserir no banco (simular) | Simular erro de DB (parar MySQL) | Mensagem de erro: "Erro ao cadastrar usuário. Tente novamente mais tarde." | ⏳ |

---

## 4. Testes de Segurança

### 4.1 Proteção contra SQL Injection

| ID | Cenário | Entrada | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| SQL-001 | Injection no nome | Nome: `admin'; DROP TABLE usuarios; --` | Rejeitado; usuário não criado; mensagem de erro genérica | ⏳ |
| SQL-002 | Injection no e-mail | E-mail: `test@ex.com' OR '1'='1` | Rejeitado; mensagem: "E-mail inválido." | ⏳ |
| SQL-003 | Payload no e-mail | E-mail: `"; UPDATE usuarios SET papel='admin'; --` | Rejeitado; prepared statement protege | ⏳ |

### 4.2 Validação de CSRF Token

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| CSRF-001 | Token ausente | Remover campo hidden `csrf_token` do form e submeter | Mensagem de erro: "Requisição inválida. Tente novamente." | ⏳ |
| CSRF-002 | Token inválido | Substituir token por valor fictício e submeter | Mensagem de erro: "Requisição inválida. Tente novamente." | ⏳ |
| CSRF-003 | Token válido | Submeter com token correto gerado pela página | Cadastro processado normalmente (se outros dados OK) | ⏳ |

### 4.3 Armazenamento Seguro de Senha

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| PWD-001 | Senha não em texto puro no DB | Cadastrar usuário; verificar campo `senha` no DB | Hash bcrypt visível (não é texto puro); ex: `$2y$12$...` | ⏳ |
| PWD-002 | password_hash() usado | Cadastrar dois usuários com mesma senha | Hashes diferentes (salt aleatório) | ⏳ |
| PWD-003 | password_verify() no login | Após cadastro, fazer login com senha correta | ✅ Login bem-sucedido | ⏳ |
| PWD-004 | password_verify() com senha errada | Após cadastro, tentar login com senha errada | ❌ Login falha; mensagem: "E-mail ou senha inválidos." | ⏳ |

---

## 5. Testes de Banco de Dados

### 5.1 Integridade de Dados

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| DB-001 | Usuário inserido na tabela | Cadastrar usuário; consultar `SELECT * FROM usuarios WHERE email='novo@ex.com'` | Registro presente com: id (PK), nome, email, senha (hash), papel='estoquista', status='ativo' | ⏳ |
| DB-002 | Constraint UNIQUE no e-mail | Tentar inserir dois usuários com mesmo e-mail via API/DB direto | Violação de constraint; erro do DB | ⏳ |
| DB-003 | Timestamps criado_em/atualizado_em | Cadastrar usuário; verificar campos `criado_em` e `atualizado_em` | Ambos preenchidos com timestamp atual | ⏳ |
| DB-004 | Valores padrão | Cadastrar usuário (não especificar papel/status) | papel='estoquista', status='ativo' (defaults) | ⏳ |

### 5.2 Rollback de Erro

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| DB-ROLL-001 | Erro durante INSERT | Simular erro (ex: disco cheio, permissão negada) | Transação não comitada; usuário não criado; mensagem ao usuário | ⏳ |

---

## 6. Testes de Fluxo Completo (Cadastro → Login)

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| FLOW-001 | Cadastro e login com sucesso | 1. Cadastrar com e-mail `novo_user@test.com`, senha `teste1234` 2. Ir ao login 3. Fazer login com mesmos dados | 1. ✅ Cadastro realizado 2. ✅ Redirecionado para login com flash de sucesso 3. ✅ Login bem-sucedido; acesso ao dashboard | ⏳ |
| FLOW-002 | Logout após cadastro e login | 1. Executar FLOW-001 2. Fazer logout | 1. ✅ Fluxo completo 2. ✅ Redirecionado para login; sessão destruída | ⏳ |
| FLOW-003 | Nova sessão após login | 1. Cadastrar novo_user2 2. Login 3. Abrir aba anônima e acessar dashboard | 1-2. ✅ Sucesso 3. ❌ Acesso negado (sem sessão) | ⏳ |

---

## 7. Testes de User Experience (UX)

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| UX-001 | Mensagens de erro amigáveis | Tentar cadastro com erros | Mensagens claras em Português; indicação de qual campo tem problema | ⏳ |
| UX-002 | Mensagem de sucesso | Cadastro bem-sucedido | Flash message verde com texto: "Cadastro realizado com sucesso. Faça login." | ⏳ |
| UX-003 | Preservação de dados em erro | Preencher nome e e-mail válidos; errar apenas a senha. Corrigir e submeter. | Dados de nome/e-mail não são pedidos novamente (campos preenchidos) ou formulário redireciona para login após sucesso | ⏳ |
| UX-004 | Responsividade do formulário | Acessar em dispositivo mobile (ou simular com DevTools) | Formulário adaptável; botões e campos acessíveis | ⏳ |

---

## 8. Testes de Regressão

| ID | Cenário | Passos | Resultado Esperado | ✅/❌ |
|---|---|---|---|---|
| REG-001 | Login com usuário seed ainda funciona | Fazer login com `admin@controleestoque.local` / `admin123` | ✅ Login bem-sucedido; acesso ao sistema | ⏳ |
| REG-002 | Dashboard acessível após login | Após login, acessar dashboard | ✅ Dashboard exibido com dados corretos | ⏳ |
| REG-003 | Outras funcionalidades (Produtos, Inventário) | Após cadastro e login, acessar outras seções | ✅ Todas as funcionalidades continuam funcionando | ⏳ |
| REG-004 | Logout continua funcionando | Fazer logout (via botão ou rota) | ✅ Sessão destruída; redirecionado para login | ⏳ |

---

## 9. Resumo de Resultados

| Categoria | Total | ✅ Passou | ❌ Falhou | ⏳ Pendente |
|-----------|-------|----------|----------|-----------|
| Navegação | 4 | 0 | 0 | 4 |
| Validação | 19 | 0 | 0 | 19 |
| Funcionalidade | 7 | 0 | 0 | 7 |
| Segurança | 14 | 0 | 0 | 14 |
| Banco de Dados | 6 | 0 | 0 | 6 |
| Fluxo Completo | 3 | 0 | 0 | 3 |
| UX | 4 | 0 | 0 | 4 |
| Regressão | 4 | 0 | 0 | 4 |
| **TOTAL** | **61** | **0** | **0** | **61** |

---

## 10. Instruções de Execução dos Testes

### Pré-requisitos
- MySQL rodando com DB `controle_estoque` criado (executar `setup.php`)
- PHP 8.0+ com PDO e pdo_mysql habilitados
- Projeto acessível em `http://localhost/Eng.Software-Controle-de-Estoque/`

### Passos
1. Abrir navegador e acessar `http://localhost/Eng.Software-Controle-de-Estoque/public/index.php?acao=cadastro`
2. Para cada teste, seguir os passos descritos na coluna "Passos"
3. Comparar resultado com "Resultado Esperado"
4. Marcar ✅ (passou) ou ❌ (falhou) na coluna respectiva
5. Se falhar, documentar o erro em uma coluna "Notas"

### Testes Críticos (Executar Primeiro)
- NAV-001, EMAIL-005, SENHA-002, SQL-002, CSRF-001, PWD-001, DB-001, FLOW-001

---

## Notas Adicionais

- **Data de Execução:** [Preenchimento manual]
- **Executado por:** [Preenchimento manual]
- **Desvios encontrados:** [Se houver]
- **Observações gerais:** [Se houver]

---

**Status Final do Teste:** ⏳ PENDENTE  
**Recomendação:** Executar testes antes de fazer merge em develop
