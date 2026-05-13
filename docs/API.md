# Documentacao da API

As APIs do sistema foram implementadas em PHP nativo e retornam JSON.

Todas as respostas seguem o formato:

```json
{
  "erro": false,
  "mensagem": "Mensagem da operacao",
  "dados": []
}
```

Em caso de erro, a resposta pode incluir a chave `erros`:

```json
{
  "erro": true,
  "mensagem": "Dados invalidos.",
  "dados": null,
  "erros": {
    "nome": "O nome do produto e obrigatorio."
  }
}
```

## Produtos

### Listar produtos

```http
GET public/index.php?acao=api_produtos
```

Filtros opcionais:

- `busca`
- `categoria`
- `unidade`
- `status`

### Buscar produto por ID

```http
GET public/index.php?acao=api_produtos&id=1
```

### Criar produto

```http
POST public/index.php?acao=api_produtos
```

Exige usuario autenticado com perfil `admin`.

Campos aceitos:

- `nome`
- `codigo`
- `categoria`
- `unidade`
- `descricao`
- `status`
- `quantidade`
- `estoque_minimo`
- `estoque_maximo`
- `preco`

### Atualizar produto

```http
PUT public/index.php?acao=api_produtos&id=1
PATCH public/index.php?acao=api_produtos&id=1
```

Exige usuario autenticado com perfil `admin`.

`PUT` atualiza todos os campos.  
`PATCH` atualiza apenas os campos enviados.

### Remover produto

```http
DELETE public/index.php?acao=api_produtos&id=1
```

Exige usuario autenticado com perfil `admin`.

## Movimentacoes

### Listar movimentacoes

```http
GET public/index.php?acao=api_movimentacoes
```

Parametro opcional:

- `limite`

### Listar movimentacoes de um produto

```http
GET public/index.php?acao=api_movimentacoes&produto_id=1
```

### Registrar movimentacao

```http
POST public/index.php?acao=api_movimentacoes
```

Campos obrigatorios:

- `produto_id`
- `tipo`
- `motivo`
- `quantidade`

Campos opcionais:

- `observacao`

Tipos aceitos:

- `entrada`
- `saida`

Motivos de entrada:

- `compra`
- `devolucao`
- `transferencia`

Motivos de saida:

- `venda`
- `consumo_interno`
- `perda`
- `avaria`

## Codigos de status

- `200`: requisicao executada com sucesso
- `201`: recurso criado com sucesso
- `400`: parametro obrigatorio ausente ou invalido
- `401`: usuario nao autenticado
- `403`: usuario sem permissao
- `404`: recurso nao encontrado
- `405`: metodo nao permitido
- `422`: dados invalidos
- `500`: erro interno
