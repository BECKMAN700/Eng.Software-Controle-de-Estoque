# Revisão: Funcionalidade de Cadastro de Usuário

**Commit:** 6ff9709  
**Mensagem:** feat: adiciona cadastro de usuario  
**Data da revisão:** 2026-06-23  
**Versão:** 1.0  

---

## 1. Resumo da Funcionalidade

A funcionalidade de cadastro de usuário permite que novos usuários criem uma conta no sistema Controle de Estoque antes de fazer login. O processo inclui:

- Formulário de cadastro com validação client-side e server-side
- Verificação de e-mail duplicado no banco de dados
- Armazenamento seguro de senha com `password_hash()`
- Redirecionamento com feedback (sucesso/erro) via flash messages
- Integração com o fluxo de login existente

---

## 2. Arquivos Alterados no Commit 6ff9709

| Arquivo | Tipo | Descrição |
|---------|------|-----------|
| `app/Controllers/AuthController.php` | Modificado | Adicionado métodos `cadastro()` e `registrarCadastro()` para exibir formulário e processar submissão |
| `app/Models/UsuarioModel.php` | Modificado | Adicionado método `cadastrar($nome, $email, $senhaHash)` com prepared statement |
| `app/Views/auth/cadastro.php` | Criado | Formulário de cadastro com CSRF token, campos de nome, e-mail, senha e confirmação |
| `app/Views/auth/login.php` | Modificado | Adicionado link "Cadastre-se" e suporte a flash messages de sucesso |
| `public/index.php` | Modificado | Adicionado suporte a parâmetro `?rota=` e rotas públicas `acao=cadastro` e `acao=cadastro_salvar` |

---

## 3. Análise de Segurança

### 3.1 Proteção contra SQL Injection
✅ **PASSOU** — Uso correto de prepared statements com `PDO::prepare()` e `execute()`

```php
// Em UsuarioModel::cadastrar()
$sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
$stmt = $this->conn->prepare($sql);
return $stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':senha' => $senhaHash
]);
```

**Evidência:** Todas as queries usam binding de parâmetros; sem concatenação de strings.

### 3.2 Armazenamento de Senha
✅ **PASSOU** — Uso de `password_hash()` com algoritmo padrão

```php
// Em AuthController::registrarCadastro()
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
$ok = $this->model->cadastrar($nome, $email, $senhaHash);
```

**Algoritmo usado:** `PASSWORD_DEFAULT` (bcrypt no PHP 8+)  
**Verificação:** `password_verify()` usado corretamente no login

```php
// Em AuthController::autenticar()
|| !password_verify($senha, $usuario['senha'])
```

**Risco:** ✅ Mitigado — senhas são hashed com salt aleatório

### 3.3 Validação de Entrada
✅ **PASSOU** — Validações implementadas em servidor

| Validação | Implementação | Risco Mitigado |
|-----------|---------------|----------------|
| Campos obrigatórios | `if ($nome === '' OR $email === '' OR $senha === '')` | Injections, valores vazios |
| E-mail válido | `filter_var($email, FILTER_VALIDATE_EMAIL)` | E-mails malformados |
| Senhas iguais | `if ($senha !== $senhaConf)` | Cadastro com erros de digitação |
| E-mail duplicado | `$this->model->buscarPorEmail($email)` | Violação de UNIQUE constraint |

**Observação:** Recomenda-se adicionar limite de tamanho de strings (ex.: `strlen($nome) <= 150`) para evitar storage de dados desnecessários.

### 3.4 CSRF (Cross-Site Request Forgery)
✅ **PASSOU** — Token CSRF implementado

```html
<input type="hidden" name="csrf_token" value="<?= esc(Sessao::getCsrfToken()) ?>">
```

Validação em servidor:
```php
if (!Sessao::validarCsrfToken($_POST['csrf_token'] ?? '')) {
    Sessao::setFlashErro('Requisição inválida. Tente novamente.');
    header('Location: index.php?acao=cadastro');
    exit;
}
```

### 3.5 Autenticação e Sessão
✅ **PASSOU** — Regeneração de ID após login

```php
session_regenerate_id(true);
```

Protege contra fixação de sessão.

---

## 4. Fluxo de Funcionamento

```
1. Usuário acessa: ?acao=cadastro
   ↓
2. AuthController::cadastro() → app/Views/auth/cadastro.php
   ↓
3. Usuário preenche formulário e submete
   ↓
4. AuthController::registrarCadastro() valida:
   - Campos não vazios
   - E-mail válido
   - Senhas iguais
   - E-mail não duplicado
   ↓
5. UsuarioModel::cadastrar() executa INSERT com prepared statement
   ↓
6. Sucesso: Redirect para login com flash "Cadastro realizado"
   Erro: Redirect para cadastro com flash de erro
   ↓
7. Usuário acessa login com novo e-mail/senha
```

---

## 5. Pontos Positivos

✅ **Segurança de senha:** `password_hash()` com algoritmo bcrypt  
✅ **Proteção SQL:** Prepared statements em todas as queries  
✅ **Validações server-side:** E-mail, campos, duplicidade  
✅ **CSRF token:** Implementado e validado  
✅ **Sessão regenerada:** Proteção contra fixação  
✅ **UX com feedback:** Flash messages para sucesso e erro  
✅ **Integração com login:** Novo usuário pode fazer login imediatamente  
✅ **Código limpo:** Segue padrão MVC; métodos bem nomeados  

---

## 6. Melhorias Recomendadas

### 6.1 Validações Adicionais (Média Prioridade)
Adicionar validações de tamanho máximo e caracteres especiais:

```php
// Em AuthController::registrarCadastro()
if (strlen($nome) > 150 || strlen($email) > 150) {
    Sessao::setFlashErro('Nome ou e-mail muito longo.');
    // ...
}
if (strlen($senha) < 8) {
    Sessao::setFlashErro('Senha deve ter no mínimo 8 caracteres.');
    // ...
}
```

### 6.2 Rate Limiting (Alta Prioridade)
Implementar limite de tentativas de cadastro por IP para evitar brute force:

```php
// Verificar quantas tentativas foram feitas nos últimos 5 minutos
// Se > 5, blocar com mensagem "Tente novamente em 5 minutos"
```

### 6.3 Confirmação de E-mail (Média Prioridade)
Adicionar verificação de e-mail antes de ativar conta:

```php
// status = 'pendente_verificacao' na criação
// Enviar e-mail com token
// Criar rota para confirmar e-mail
```

### 6.4 Logging Detalhado (Baixa Prioridade)
Registrar tentativas de cadastro duplicado para auditoria:

```php
error_log("Tentativa de cadastro com e-mail duplicado: {$email}");
```

### 6.5 Validação Client-Side (Baixa Prioridade)
Adicionar JavaScript para feedback imediato:

```javascript
// Validar senhas iguais em tempo real
// Mostrar requisitos de senha
// Desabilitar botão até validações passarem
```

---

## 7. Testes Executados

### Teste Manual de Segurança

| Cenário | Entrada | Esperado | Resultado |
|---------|---------|----------|-----------|
| SQL Injection no e-mail | `admin@test.com' OR '1'='1` | Rejeita e-mail inválido | ✅ PASSOU |
| Força bruta (múltiplas senhas) | Vários passwords | Sem rate limiting | ⚠️ Recomenda-se implementar |
| E-mail duplicado | E-mail já existente | Mensagem de erro | ✅ PASSOU |
| Senha fraca | Senha < 8 chars | Aceita (sem validação de força) | ⚠️ Recomenda-se adicionar |
| CSRF token inválido | Token falsificado | Rejeita requisição | ✅ PASSOU |

---

## 8. Conclusão

A funcionalidade de cadastro de usuário foi implementada com **boas práticas de segurança**. As vulnerabilidades críticas (SQL injection, senhas em texto puro, CSRF) foram mitigadas. 

**Recomendação:** A feature está **apta para merge** na branch `develop` após implementação das melhorias sugeridas (rate limiting e validação de comprimento de senha).

**Status:** ✅ **APROVADO COM RECOMENDAÇÕES**

---

## Apêndice: Referências

- [OWASP: Password Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
- [PHP: password_hash()](https://www.php.net/manual/en/function.password-hash.php)
- [PHP: Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
- [OWASP: CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
