# Historias de Usuario

Formato: **"Como `<ator>`, quero `<objetivo>`, para `<beneficio>`."**
Cada historia traz criterios de aceite, os requisitos atendidos (RF) e a sprint de entrega.

## Epico 1 - Autenticacao e acesso (Sprint 2)

### US01 - Login
Como **administrador ou estoquista**, quero acessar o sistema com e-mail e senha, para usar as funcionalidades com seguranca.
- [ ] Credenciais validas concedem acesso.
- [ ] Credenciais invalidas sao recusadas com mensagem clara.
- [ ] A senha e verificada por hash (password_verify).
- RF: RF01, RF03

### US02 - Logout
Como **usuario logado**, quero encerrar a sessao, para proteger meu acesso ao sair.
- [ ] O logout limpa a sessao.
- [ ] Apos o logout, rotas internas redirecionam para o login.
- RF: RF02

### US03 - Controle por papel
Como **administrador**, quero que acoes criticas sejam restritas ao meu perfil, para evitar alteracoes indevidas pelo estoquista.
- [ ] Estoquista nao acessa exclusao de produto nem gestao de usuarios.
- [ ] O menu oculta opcoes proibidas para o estoquista.
- RF: RF03, RF07

### US04 - Gestao de usuarios
Como **administrador**, quero cadastrar e listar usuarios, para controlar quem acessa o sistema.
- [ ] O admin cadastra novos usuarios com papel definido.
- [ ] A listagem mostra os usuarios cadastrados.
- RF: RF04

## Epico 2 - Produtos (Sprint 1)

### US05 - Cadastrar produto
Como **administrador**, quero cadastrar produtos com seus dados, para registrar os itens do estoque.
- [ ] Produto valido e salvo e aparece na listagem.
- [ ] Dados invalidos (nome vazio, quantidade ou preco negativo, status invalido) sao rejeitados.
- RF: RF05

### US06 - Editar produto
Como **administrador**, quero editar um produto, para corrigir ou atualizar seus dados.
- [ ] As alteracoes sao persistidas.
- [ ] As mesmas validacoes do cadastro sao aplicadas.
- RF: RF05

### US07 - Excluir produto
Como **administrador**, quero excluir um produto, para remover itens que nao fazem mais parte do estoque.
- [ ] A exclusao exige confirmacao e perfil admin.
- [ ] O estoquista nao consegue excluir.
- RF: RF05, RF07

### US08 - Buscar e filtrar produtos
Como **estoquista**, quero buscar e filtrar produtos, para encontrar itens rapidamente.
- [ ] E possivel filtrar por nome, codigo, categoria, unidade, status e periodo.
- [ ] Datas invalidas sao bloqueadas.
- RF: RF06

## Epico 3 - Movimentacoes (Sprint 1)

### US09 - Registrar entrada
Como **estoquista**, quero registrar entradas de estoque, para refletir as compras e devolucoes.
- [ ] A entrada aumenta a quantidade do produto.
- [ ] A movimentacao fica no historico.
- RF: RF08, RF09, RF11

### US10 - Registrar saida
Como **estoquista**, quero registrar saidas de estoque, para refletir vendas e consumos, sem permitir estoque negativo.
- [ ] A saida diminui a quantidade do produto.
- [ ] Saida maior que o disponivel e rejeitada.
- RF: RF08, RF09, RF10, RF11

### US11 - Consultar historico
Como **estoquista**, quero consultar o historico de um produto, para acompanhar suas movimentacoes.
- [ ] O historico lista entradas e saidas com data, tipo, quantidade e responsavel.
- RF: RF11

## Epico 4 - Limites e alertas (Sprint 1)

### US12 - Definir limites de estoque
Como **administrador**, quero definir estoque minimo e maximo, para orientar reposicao e evitar excesso.
- [ ] Os limites sao salvos por produto.
- RF: RF12

### US13 - Visualizar alertas
Como **estoquista**, quero ver alertas de estoque, para agir sobre itens criticos.
- [ ] Produtos abaixo do minimo, no minimo e acima do maximo sao sinalizados.
- RF: RF13

## Epico 5 - Inventario e auditoria (Sprint 4)

### US14 - Abrir inventario
Como **estoquista**, quero abrir um inventario por todos os produtos ou por categoria, para iniciar uma conferencia fisica.
- [ ] O inventario grava a quantidade do sistema no momento da abertura.
- RF: RF14, RF15

### US15 - Registrar contagem
Como **estoquista**, quero registrar a contagem fisica, para comparar com o saldo do sistema.
- [ ] Contagem negativa ou nao inteira e rejeitada.
- [ ] E possivel salvar contagens parciais.
- RF: RF16

### US16 - Visualizar divergencias
Como **administrador**, quero visualizar as divergencias, para avaliar o impacto antes de aprovar.
- [ ] O relatorio mostra falta, sobra, itens conferidos e pendentes.
- RF: RF17

### US17 - Aprovar ajustes e auditar
Como **administrador**, quero aprovar os ajustes, para atualizar o estoque conforme a contagem, mantendo registro de auditoria.
- [ ] Apenas o admin aprova.
- [ ] A aprovacao atualiza a quantidade do produto e registra a auditoria.
- [ ] Inventario aprovado nao aceita nova contagem.
- RF: RF18, RF19

## Epico 6 - Relatorios e dashboard (Sprint 5)

### US18 - Relatorio de giro
Como **gestor**, quero ver o giro de estoque, para identificar produtos mais e menos movimentados.
- [ ] Os produtos aparecem classificados em alto, medio e baixo giro.
- RF: RF20

### US19 - Relatorio de valorizacao
Como **gestor**, quero ver a valorizacao do estoque, para saber o valor financeiro investido.
- [ ] O relatorio calcula valor por produto (quantidade x preco) e o total geral.
- RF: RF21

### US20 - Movimentacoes por periodo
Como **gestor**, quero consultar movimentacoes por periodo, para analisar entradas e saidas num intervalo.
- [ ] Datas invalidas sao bloqueadas; datas validas retornam os dados e totais.
- RF: RF22, RF25

### US21 - Dashboard
Como **gestor**, quero um dashboard com indicadores, para ter uma visao rapida do estoque.
- [ ] Exibe cards e graficos com dados reais.
- RF: RF23

### US22 - Exportar relatorios
Como **gestor**, quero exportar relatorios em PDF e CSV, para compartilhar e arquivar.
- [ ] Cada relatorio exporta em PDF e em CSV com os dados exibidos.
- RF: RF24

### US23 - Filtrar relatorios
Como **gestor**, quero filtrar relatorios por periodo e categoria, para refinar a analise.
- [ ] Os filtros sao aplicados aos resultados e a exportacao.
- RF: RF25

## Epico 7 - API JSON (Sprint 3)

### US24 - API de produtos
Como **integrador/desenvolvedor**, quero consumir uma API JSON de produtos, para integrar com outras aplicacoes.
- [ ] GET lista e detalha produtos; POST/PUT/PATCH/DELETE respeitam validacao e permissao.
- [ ] Erros retornam 401, 403, 404 ou 422 em JSON.
- RF: RF26

### US25 - API de movimentacoes
Como **integrador/desenvolvedor**, quero consumir uma API JSON de movimentacoes, para registrar e consultar movimentos.
- [ ] GET lista movimentacoes; POST registra com validacao.
- RF: RF27
