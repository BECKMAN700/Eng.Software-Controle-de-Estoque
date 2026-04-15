
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Controle de Estoque</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .filtros {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
        }

        .campo-filtro {
            margin-bottom: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        .acoes a {
            margin-right: 8px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>Controle de Estoque</h1>

    <p>
        <a href="index.php?acao=criar">Cadastrar novo produto</a>
    </p>

    <form class="filtros" action="index.php" method="GET">
        <input type="hidden" name="acao" value="listar">

        <div class="campo-filtro">
            <label>Buscar por nome ou código:</label><br>
            <input type="text" name="busca" value="<?= htmlspecialchars($busca ?? '') ?>">
        </div>

        <div class="campo-filtro">
            <label>Categoria:</label><br>
            <select name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>" <?= (($categoria ?? '') === $item) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-filtro">
            <label>Unidade:</label><br>
            <select name="unidade">
                <option value="">Todas</option>
                <?php foreach ($unidades as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>" <?= (($unidade ?? '') === $item) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-filtro">
            <label>Status:</label><br>
            <select name="status">
                <option value="">Todos</option>
                <?php foreach ($statusOptions as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>" <?= (($status ?? '') === $item) ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($item)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Filtrar</button>
        <a href="index.php?acao=listar">Limpar filtros</a>
    </form>

    <?php
        $temFiltrosAtivos = (
            ($busca ?? '') !== '' ||
            ($categoria ?? '') !== '' ||
            ($unidade ?? '') !== '' ||
            ($status ?? '') !== ''
        );
... (49 linhas)

message.txt
6 KB
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Entrada</title>
</head>
<body>
    <h1>Registrar Entrada de Estoque</h1>

    <p><strong>Produto:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
    <p><strong>Quantidade atual:</strong> <?= (int) $produto['quantidade'] ?></p>

    <form action="index.php?acao=entrada" method="POST">
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">

        <p>
            <label>Motivo da entrada:</label><br>
            <select name="motivo" required>
                <option value="compra">Compra</option>
                <option value="devolucao">Devolução</option>
                <option value="transferencia">Transferência</option>
            </select>
        </p>

        <p>
            <label>Observação:</label><br>
            <textarea name="observacao" rows="4" cols="40"></textarea>
        </p>

        <p>
            <label>Quantidade:</label><br>
            <input type="number" name="quantidade" min="1" required>
        </p>

        <button type="submit">Confirmar entrada</button>
        <a href="index.php?acao=listar">Voltar</a>
    </form>
</body>
</html>
<?php

class ProdutoModel
{
    private $caminhoArquivo;

message.txt
11 KB
﻿
<?php

class ProdutoModel
{
    private $caminhoArquivo;

    public function __construct()
    {
        $this->caminhoArquivo = __DIR__ . '/../../data/produtos.json';

        if (!file_exists($this->caminhoArquivo)) {
            file_put_contents($this->caminhoArquivo, json_encode([]));
        }
    }

    private function lerDados()
    {
        $json = file_get_contents($this->caminhoArquivo);
        $dados = json_decode($json, true);

        return is_array($dados) ? $dados : [];
    }

    private function salvarDados($produtos)
    {
        file_put_contents(
            $this->caminhoArquivo,
            json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function normalizarTexto($valor)
    {
        return strtolower(trim((string) $valor));
    }

    private function contemTexto($texto, $busca)
    {
        return strpos($texto, $busca) !== false;
    }

    public function listar()
    {
        return $this->lerDados();
    }

    public function listarFiltrados($busca = '', $categoria = '', $unidade = '', $status = '')
    {
        $produtos = $this->lerDados();

        $busca = $this->normalizarTexto($busca);
        $categoria = $this->normalizarTexto($categoria);
        $unidade = $this->normalizarTexto($unidade);
        $status = $this->normalizarTexto($status);

        $filtrados = array_filter($produtos, function ($produto) use ($busca, $categoria, $unidade, $status) {
            $nome = $this->normalizarTexto($produto['nome'] ?? '');
            $codigo = $this->normalizarTexto($produto['codigo'] ?? '');
            $produtoCategoria = $this->normalizarTexto($produto['categoria'] ?? '');
            $produtoUnidade = $this->normalizarTexto($produto['unidade'] ?? '');
            $produtoStatus = $this->normalizarTexto($produto['status'] ?? '');

            $passaBusca = true;
            if ($busca !== '') {
                $passaBusca = $this->contemTexto($nome, $busca) || $this->contemTexto($codigo, $busca);
            }

            $passaCategoria = ($categoria === '' || $produtoCategoria === $categoria);
            $passaUnidade = ($unidade === '' || $produtoUnidade === $unidade);
            $passaStatus = ($status === '' || $produtoStatus === $status);

            return $passaBusca && $passaCategoria && $passaUnidade && $passaStatus;
        });

        return array_values($filtrados);
    }

    public function listarCategorias()
    {
        $produtos = $this->lerDados();
        $categorias = [];

        foreach ($produtos as $produto) {
            $categoria = trim($produto['categoria'] ?? '');
            if ($categoria !== '') {
                $categorias[] = $categoria;
            }
        }

        $categorias = array_values(array_unique($categorias));
        natcasesort($categorias);

        return array_values($categorias);
    }

    public function listarUnidades()
    {
        $produtos = $this->lerDados();
        $unidades = [];

        foreach ($produtos as $produto) {
            $unidade = trim($produto['unidade'] ?? '');
            if ($unidade !== '') {
                $unidades[] = $unidade;
            }
        }

        $unidades = array_values(array_unique($unidades));
        natcasesort($unidades);

        return array_values($unidades);
    }

    public function buscarPorId($id)
    {
        $produtos = $this->lerDados();

        foreach ($produtos as $produto) {
            if ($produto['id'] == $id) {
                return $produto;
            }
        }

        return null;
    }

    public function criar($dados)
    {
        $produtos = $this->lerDados();

        $novoId = 1;
        if (!empty($produtos)) {
            $ids = array_column($produtos, 'id');
            $novoId = max($ids) + 1;
        }

        $novoProduto = [
            'id' => $novoId,
            'nome' => $dados['nome'],
            'codigo' => $dados['codigo'],
            'categoria' => $dados['categoria'],
            'unidade' => $dados['unidade'],
            'descricao' => $dados['descricao'],
            'status' => $dados['status'],
            'quantidade' => (int) $dados['quantidade'],
            'preco' => (float) $dados['preco'],
            'historico_movimentacoes' => []
        ];

        $produtos[] = $novoProduto;
        $this->salvarDados($produtos);
    }

    public function atualizar($id, $dados)
    {
        $produtos = $this->lerDados();

        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                $produto['nome'] = $dados['nome'];
                $produto['codigo'] = $dados['codigo'];
                $produto['categoria'] = $dados['categoria'];
                $produto['unidade'] = $dados['unidade'];
                $produto['descricao'] = $dados['descricao'];
                $produto['status'] = $dados['status'];
                $produto['quantidade'] = (int) $dados['quantidade'];
                $produto['preco'] = (float) $dados['preco'];

                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }

                break;
            }
        }

        $this->salvarDados($produtos);
    }

    public function excluir($id)
    {
        $produtos = $this->lerDados();

        $produtos = array_filter($produtos, function ($produto) use ($id) {
            return $produto['id'] != $id;
        });

        $this->salvarDados(array_values($produtos));
    }

    public function movimentar($id, $tipo, $quantidade, $observacao = '')
    {
        $produtos = $this->lerDados();
        
        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                $quantidade = (int) $quantidade;

                if ($quantidade <= 0) {
                    return false;
                }

                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }

                if ($tipo === 'entrada') {
                    $produto['quantidade'] += $quantidade;
                    $motivo = 'entrada_manual';
                } elseif ($tipo === 'saida') {
                    if ((int) $produto['quantidade'] < $quantidade) {
                        return false;
                    }

                    $produto['quantidade'] -= $quantidade;
                    $motivo = 'saida_manual';
                } else {
                    return false;
                }

                $produto['historico_movimentacoes'][] = [
                    'tipo' => $tipo,
                    'motivo' => $motivo,
                    'quantidade' => $quantidade,
                    'observacao' => trim($observacao),
                    'data_hora' => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s')
                ];

                $this->salvarDados($produtos);
                return true;
            }
        }

        return false;
    }

    public function registrarEntrada($id, $motivo, $quantidade, $observacao = '')
    {
        $motivosValidos = ['compra', 'devolucao', 'transferencia'];
        $quantidade = (int) $quantidade;

        if ($quantidade <= 0 || !in_array($motivo, $motivosValidos, true)) {
            return false;
        }

        $produtos = $this->lerDados();

        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                $produto['quantidade'] += $quantidade;

                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }

                $produto['historico_movimentacoes'][] = [
                    'tipo' => 'entrada',
                    'motivo' => $motivo,
                    'quantidade' => $quantidade,
                    'observacao' => trim($observacao),
                    'data_hora' => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s')
                ];

                $this->salvarDados($produtos);
                return true;
            }
        }

        return false;
    }

    public function registrarSaida($id, $motivo, $quantidade, $observacao = '')
    {
        $motivosValidos = ['venda', 'consumo_interno', 'perda', 'avaria'];
        $quantidade = (int) $quantidade;

        if ($quantidade <= 0 || !in_array($motivo, $motivosValidos, true)) {
            return false;
        }

        $produtos = $this->lerDados();

        foreach ($produtos as &$produto) {
            if ($produto['id'] == $id) {
                if ((int) $produto['quantidade'] < $quantidade) {
                    return false;
                }

                $produto['quantidade'] -= $quantidade;

                if (!isset($produto['historico_movimentacoes']) || !is_array($produto['historico_movimentacoes'])) {
                    $produto['historico_movimentacoes'] = [];
                }

                $produto['historico_movimentacoes'][] = [
                    'tipo' => 'saida',
                    'motivo' => $motivo,
                    'quantidade' => $quantidade,
                    'observacao' => trim($observacao),
                    'data_hora' => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s')
                ];

                $this->salvarDados($produtos);
                return true;
            }
        }

        return false;
    }

    public function buscarHistoricoPorProduto($id)
    {
        $produto = $this->buscarPorId($id);

        if (!$produto) {
            return [];
        }

        $historico = $produto['historico_movimentacoes'] ?? [];

        usort($historico, function ($a, $b) {
            return strtotime($b['data_hora'] ?? '') <=> strtotime($a['data_hora'] ?? '');
        });

        return $historico;
    }
}
message.txt
11 KB