# Casos de Uso

Casos de uso principais do sistema. Atores: **Administrador** e **Estoquista**.

---

## UC01 - Realizar login

- **Ator principal:** Administrador ou Estoquista
- **Pre-condicoes:** o usuario possui cadastro ativo no sistema.

**Fluxo principal**
1. O usuario acessa a tela de login.
2. Informa e-mail e senha.
3. O sistema valida a senha com `password_verify`.
4. O sistema cria a sessao com id, nome, e-mail e papel.
5. O usuario e redirecionado para a area interna.

**Fluxos alternativos**
- 3a. Senha invalida: o sistema exibe mensagem de erro e mantem o usuario na tela de login.

**Pos-condicoes:** sessao iniciada; o sistema reconhece o papel do usuario.

---

## UC02 - Cadastrar produto

- **Ator principal:** Administrador
- **Pre-condicoes:** estar autenticado como admin.

**Fluxo principal**
1. O admin acessa o cadastro de produto.
2. Informa nome, codigo, categoria, unidade, quantidade, limites, preco e status.
3. O sistema valida os dados.
4. O sistema salva o produto e o exibe na listagem.

**Fluxos alternativos**
- 3a. Dados invalidos (nome vazio, quantidade ou preco negativo, estoque maximo menor que o minimo, status invalido): o sistema rejeita e destaca os campos.

**Pos-condicoes:** produto cadastrado.

---

## UC03 - Registrar saida de estoque

- **Ator principal:** Estoquista
- **Pre-condicoes:** existir produto com saldo; estar autenticado.

**Fluxo principal**
1. O estoquista seleciona o produto e a opcao de saida.
2. Informa quantidade e motivo.
3. O sistema valida a movimentacao.
4. O sistema diminui a quantidade do produto e grava a movimentacao no historico.

**Fluxos alternativos**
- 3a. Quantidade maior que o saldo disponivel: o sistema rejeita a saida.
- 3b. Tipo ou motivo invalido: o sistema rejeita com mensagem.

**Pos-condicoes:** saldo atualizado; movimentacao registrada.

---

## UC04 - Realizar inventario e aprovar ajuste

- **Ator principal:** Estoquista (abertura e contagem) e Administrador (aprovacao)
- **Pre-condicoes:** existir produtos cadastrados.

**Fluxo principal**
1. O estoquista abre um inventario (todos os produtos ou por categoria).
2. O sistema grava a quantidade do sistema de cada item no momento da abertura.
3. O estoquista registra a contagem fisica.
4. O sistema calcula a divergencia (contagem - sistema).
5. O administrador consulta as divergencias.
6. O administrador aprova os ajustes.
7. O sistema atualiza a quantidade dos produtos e registra a auditoria.

**Fluxos alternativos**
- 3a. Contagem negativa ou nao inteira: o sistema rejeita.
- 6a. Usuario nao-admin tenta aprovar: o sistema bloqueia.
- 6b. Existe item pendente de contagem: o sistema impede a aprovacao.

**Pos-condicoes:** estoque ajustado; auditoria registrada; inventario aprovado nao aceita nova contagem.

---

## UC05 - Gerar e exportar relatorio

- **Ator principal:** Gestor (administrador)
- **Pre-condicoes:** existir dados de produtos e movimentacoes.

**Fluxo principal**
1. O gestor acessa o painel de relatorios.
2. Escolhe o relatorio (giro, valorizacao ou movimentacoes por periodo).
3. Aplica filtros (periodo, categoria).
4. O sistema valida os filtros e exibe os resultados.
5. O gestor exporta em PDF ou CSV.

**Fluxos alternativos**
- 4a. Datas invalidas: o sistema bloqueia e solicita correcao.

**Pos-condicoes:** relatorio exibido e, opcionalmente, exportado.

---

## UC06 - Consumir API de produtos

- **Ator principal:** Integrador/Desenvolvedor (consumidor da API)
- **Pre-condicoes:** estar autenticado; acoes de escrita exigem admin.

**Fluxo principal**
1. O cliente envia uma requisicao (GET, POST, PUT, PATCH ou DELETE) para a API de produtos.
2. O sistema autentica e autoriza a requisicao.
3. O sistema valida os dados (escrita) e executa a operacao.
4. O sistema responde em JSON no padrao `erro`, `mensagem`, `dados`.

**Fluxos alternativos**
- 2a. Sem login: retorna 401.
- 2b. Sem permissao: retorna 403.
- 3a. Recurso inexistente: retorna 404.
- 3b. Dados invalidos: retorna 422.

**Pos-condicoes:** operacao executada ou erro padronizado retornado.
