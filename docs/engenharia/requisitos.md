# Documento de Requisitos

## Sistema de Controle de Estoque - Engenharia de Software (UFT 2026.1)

## 1. Visao geral

O Controle de Estoque e um sistema web para gerenciar produtos, movimentacoes (entradas e saidas), limites de estoque, usuarios, inventario, auditoria, relatorios gerenciais e exportacoes. O objetivo e dar controle e visibilidade sobre o estoque, apoiando decisoes com dados reais.

- **Plataforma:** aplicacao web (PHP nativo, arquitetura MVC).
- **Banco de dados:** MySQL, acessado via PDO.
- **Perfis de acesso:** administrador e estoquista.

## 2. Atores

| Ator | Descricao |
| --- | --- |
| **Administrador** | Acesso total. Gerencia produtos e usuarios, aprova ajustes de inventario e consulta a auditoria. |
| **Estoquista** | Perfil operacional. Registra movimentacoes, realiza contagens de inventario e consulta relatorios. Nao exclui produtos, nao gerencia usuarios e nao aprova inventario. |
| **Gestor (beneficiario)** | Consome relatorios e o dashboard para apoiar decisoes. Papel exercido pelo administrador. |

## 3. Requisitos Funcionais (RF)

### Autenticacao e usuarios
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF01 | O sistema deve permitir login com e-mail e senha. | Alta |
| RF02 | O sistema deve encerrar a sessao no logout. | Alta |
| RF03 | O sistema deve diferenciar os papeis administrador e estoquista. | Alta |
| RF04 | O administrador deve poder cadastrar e listar usuarios. | Media |

### Produtos
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF05 | O sistema deve permitir cadastrar, listar, editar e excluir produtos. | Alta |
| RF06 | O sistema deve permitir buscar e filtrar produtos por nome, codigo, categoria, unidade, status e periodo. | Alta |
| RF07 | A exclusao de produto deve ser restrita ao administrador. | Alta |

### Movimentacoes
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF08 | O sistema deve registrar entradas e saidas de estoque. | Alta |
| RF09 | O sistema deve atualizar a quantidade do produto a cada movimentacao. | Alta |
| RF10 | O sistema deve impedir saida maior que a quantidade disponivel. | Alta |
| RF11 | O sistema deve manter historico de movimentacoes por produto. | Alta |

### Limites e alertas
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF12 | O sistema deve permitir definir estoque minimo e maximo por produto. | Media |
| RF13 | O sistema deve sinalizar produtos abaixo do minimo, no minimo e acima do maximo. | Alta |

### Inventario e auditoria
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF14 | O sistema deve permitir abrir inventario por todos os produtos ou por categoria. | Alta |
| RF15 | O sistema deve registrar a quantidade do sistema no momento da abertura. | Alta |
| RF16 | O sistema deve permitir registrar a contagem fisica manual. | Alta |
| RF17 | O sistema deve calcular a divergencia entre contagem e sistema. | Alta |
| RF18 | A aprovacao de ajustes deve ser restrita ao administrador. | Alta |
| RF19 | A aprovacao deve atualizar a quantidade do produto e registrar auditoria. | Alta |

### Relatorios e dashboard
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF20 | O sistema deve gerar relatorio de giro de estoque. | Alta |
| RF21 | O sistema deve gerar relatorio de valorizacao do estoque. | Alta |
| RF22 | O sistema deve gerar relatorio de movimentacoes por periodo. | Alta |
| RF23 | O sistema deve exibir um dashboard com cards e graficos. | Alta |
| RF24 | O sistema deve permitir exportar relatorios em PDF e CSV. | Media |
| RF25 | O sistema deve permitir filtrar relatorios por periodo e categoria. | Alta |

### API
| ID | Requisito | Prioridade |
| --- | --- | --- |
| RF26 | O sistema deve expor uma API JSON para produtos (GET, POST, PUT, PATCH, DELETE). | Media |
| RF27 | O sistema deve expor uma API JSON para movimentacoes (GET, POST). | Media |

## 4. Requisitos Nao-Funcionais (RNF)

| ID | Requisito | Categoria |
| --- | --- | --- |
| RNF01 | As senhas devem ser armazenadas como hash (password_hash), nunca em texto puro. | Seguranca |
| RNF02 | POSTs criticos devem usar token CSRF. | Seguranca |
| RNF03 | As consultas ao banco devem usar prepared statements (PDO), evitando SQL Injection. | Seguranca |
| RNF04 | Rotas internas devem exigir autenticacao; acoes criticas devem exigir o papel admin. | Seguranca |
| RNF05 | A aplicacao deve seguir o padrao MVC em PHP nativo, sem framework de aplicacao. | Arquitetura |
| RNF06 | O codigo deve possuir testes automatizados executaveis (PHPUnit). | Qualidade |
| RNF07 | O sistema deve exibir mensagens claras de erro e sucesso e validar as entradas. | Usabilidade |
| RNF08 | O sistema deve executar em ambiente XAMPP (PHP 8.x + MySQL). | Portabilidade |
| RNF09 | O versionamento deve seguir GitFlow, com Pull Requests revisados por outro integrante. | Manutenibilidade |
| RNF10 | A API nao deve retornar HTML nem avisos junto ao JSON e deve usar os codigos HTTP corretos (401, 403, 404, 422). | Confiabilidade |
