<?php

require_once __DIR__ . '/../../config/Database.php';

class ProdutoModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    private function valorOuNull($valor)
    {
        $valor = trim((string) $valor);
        return $valor === '' ? null : $valor;
    }

    private function anexarHistoricoAoProduto($produto)
    {
        if (!$produto) {
            return null;
        }

        $produto['historico_movimentacoes'] = $this->buscarHistoricoPorProduto($produto['id']);
        return $produto;
    }

    public function listar()
    {
        $sql = "SELECT * FROM produtos ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarFiltrados($busca = '', $categoria = '', $unidade = '', $status = '', $dataInicial = '', $dataFinal = '')
    {
        $sql = "SELECT * FROM produtos WHERE 1=1";
        $params = [];

        $busca = trim((string) $busca);
        $categoria = trim((string) $categoria);
        $unidade = trim((string) $unidade);
        $status = trim((string) $status);
        $dataInicial = trim((string) $dataInicial);
        $dataFinal = trim((string) $dataFinal);

        if ($busca !== '') {
            $sql .= " AND (nome LIKE :busca OR codigo LIKE :busca)";
            $params[':busca'] = '%' . $busca . '%';
        }

        if ($categoria !== '') {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        if ($unidade !== '') {
            $sql .= " AND unidade = :unidade";
            $params[':unidade'] = $unidade;
        }

        if ($status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if ($dataInicial !== '' && $dataFinal !== '') {
            $sql .= " AND criado_em BETWEEN :data_inicial AND :data_final";
            $params[':data_inicial'] = $dataInicial . ' 00:00:00';
            $params[':data_final'] = $dataFinal . ' 23:59:59';
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCategorias()
    {
        $sql = "SELECT DISTINCT categoria
                FROM produtos
                WHERE categoria IS NOT NULL
                  AND TRIM(categoria) <> ''
                ORDER BY categoria ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function listarUnidades()
    {
        $sql = "SELECT DISTINCT unidade
                FROM produtos
                WHERE unidade IS NOT NULL
                  AND TRIM(unidade) <> ''
                ORDER BY unidade ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM produtos WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->anexarHistoricoAoProduto($produto);
    }

    public function listarAbaixoDoMinimo(): array
    {
        $sql = "SELECT id, nome, codigo, categoria, unidade, quantidade, estoque_minimo
                FROM produtos
                WHERE status = 'ativo'
                  AND quantidade < estoque_minimo
                ORDER BY quantidade ASC, nome ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarNoMinimo(): array
    {
        $sql = "SELECT id, nome, codigo, categoria, unidade, quantidade, estoque_minimo
                FROM produtos
                WHERE status = 'ativo'
                  AND estoque_minimo > 0
                  AND quantidade = estoque_minimo
                ORDER BY nome ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAcimaDoMaximo(): array
    {
        $sql = "SELECT id, nome, codigo, categoria, unidade, quantidade, estoque_maximo
                FROM produtos
                WHERE status = 'ativo'
                  AND estoque_maximo IS NOT NULL
                  AND quantidade > estoque_maximo
                ORDER BY quantidade DESC, nome ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($dados)
    {
        try {
            $sql = "INSERT INTO produtos
                    (nome, codigo, categoria, unidade, descricao, status, quantidade, estoque_minimo, estoque_maximo, preco)
                    VALUES
                    (:nome, :codigo, :categoria, :unidade, :descricao, :status, :quantidade, :estoque_minimo, :estoque_maximo, :preco)";

            $stmt = $this->conn->prepare($sql);

            $estoqueMaximo = ($dados['estoque_maximo'] ?? '') !== '' ? (int) $dados['estoque_maximo'] : null;

            $sucesso = $stmt->execute([
                ':nome' => trim((string) ($dados['nome'] ?? '')),
                ':codigo' => $this->valorOuNull($dados['codigo'] ?? null),
                ':categoria' => $this->valorOuNull($dados['categoria'] ?? null),
                ':unidade' => $this->valorOuNull($dados['unidade'] ?? null),
                ':descricao' => $this->valorOuNull($dados['descricao'] ?? null),
                ':status' => trim((string) ($dados['status'] ?? 'ativo')),
                ':quantidade' => (int) ($dados['quantidade'] ?? 0),
                ':estoque_minimo' => (int) ($dados['estoque_minimo'] ?? 0),
                ':estoque_maximo' => $estoqueMaximo,
                ':preco' => (float) ($dados['preco'] ?? 0)
            ]);

            if (!$sucesso) {
                return false;
            }

            return (int) $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Erro ao criar produto.', 0, $e);
        }
    }

    public function atualizar($id, $dados)
    {
        try {
            $sql = "UPDATE produtos SET
                        nome = :nome,
                        codigo = :codigo,
                        categoria = :categoria,
                        unidade = :unidade,
                        descricao = :descricao,
                        status = :status,
                        quantidade = :quantidade,
                        estoque_minimo = :estoque_minimo,
                        estoque_maximo = :estoque_maximo,
                        preco = :preco
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $estoqueMaximo = ($dados['estoque_maximo'] ?? '') !== '' ? (int) $dados['estoque_maximo'] : null;

            return $stmt->execute([
                ':id' => (int) $id,
                ':nome' => trim((string) ($dados['nome'] ?? '')),
                ':codigo' => $this->valorOuNull($dados['codigo'] ?? null),
                ':categoria' => $this->valorOuNull($dados['categoria'] ?? null),
                ':unidade' => $this->valorOuNull($dados['unidade'] ?? null),
                ':descricao' => $this->valorOuNull($dados['descricao'] ?? null),
                ':status' => trim((string) ($dados['status'] ?? 'ativo')),
                ':quantidade' => (int) ($dados['quantidade'] ?? 0),
                ':estoque_minimo' => (int) ($dados['estoque_minimo'] ?? 0),
                ':estoque_maximo' => $estoqueMaximo,
                ':preco' => (float) ($dados['preco'] ?? 0)
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Erro ao atualizar produto.', 0, $e);
        }
    }

    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id' => (int) $id
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Erro ao excluir produto.', 0, $e);
        }
    }

    public function movimentar($id, $tipo, $quantidade, $observacao = '', $usuarioId = null)
    {
        $quantidade = (int) $quantidade;

        if ($quantidade <= 0) {
            return false;
        }

        $produto = $this->buscarPorId($id);

        if (!$produto) {
            return false;
        }

        if ($tipo !== 'entrada' && $tipo !== 'saida') {
            return false;
        }

        if ($tipo === 'saida' && (int) $produto['quantidade'] < $quantidade) {
            return false;
        }

        $motivo = ($tipo === 'entrada') ? 'entrada_manual' : 'saida_manual';

        try {
            $this->conn->beginTransaction();

            if ($tipo === 'entrada') {
                $sqlProduto = "UPDATE produtos
                               SET quantidade = quantidade + :quantidade
                               WHERE id = :id";
            } else {
                $sqlProduto = "UPDATE produtos
                               SET quantidade = quantidade - :quantidade
                               WHERE id = :id
                                 AND quantidade >= :quantidade";
            }

            $stmtProduto = $this->conn->prepare($sqlProduto);
            $stmtProduto->execute([
                ':quantidade' => $quantidade,
                ':id' => (int) $id
            ]);

            if ($stmtProduto->rowCount() !== 1) {
                $this->conn->rollBack();
                return false;
            }

            $sqlMov = "INSERT INTO movimentacoes
                       (produto_id, tipo, motivo, quantidade, observacao, usuario_id)
                       VALUES
                       (:produto_id, :tipo, :motivo, :quantidade, :observacao, :usuario_id)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->execute([
                ':produto_id' => (int) $id,
                ':tipo' => $tipo,
                ':motivo' => $motivo,
                ':quantidade' => $quantidade,
                ':observacao' => trim((string) $observacao),
                ':usuario_id' => $usuarioId
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new RuntimeException('Erro ao movimentar produto.', 0, $e);
        }
    }

    public function registrarEntrada($id, $motivo, $quantidade, $observacao = '', $usuarioId = null)
    {
        $motivosValidos = ['compra', 'devolucao', 'transferencia'];
        $quantidade = (int) $quantidade;
        $motivo = trim((string) $motivo);

        if ($quantidade <= 0 || !in_array($motivo, $motivosValidos, true)) {
            return false;
        }

        $produto = $this->buscarPorId($id);

        if (!$produto) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $sqlProduto = "UPDATE produtos
                           SET quantidade = quantidade + :quantidade
                           WHERE id = :id";

            $stmtProduto = $this->conn->prepare($sqlProduto);
            $stmtProduto->execute([
                ':quantidade' => $quantidade,
                ':id' => (int) $id
            ]);

            if ($stmtProduto->rowCount() !== 1) {
                $this->conn->rollBack();
                return false;
            }

            $sqlMov = "INSERT INTO movimentacoes
                       (produto_id, tipo, motivo, quantidade, observacao, usuario_id)
                       VALUES
                       (:produto_id, 'entrada', :motivo, :quantidade, :observacao, :usuario_id)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->execute([
                ':produto_id' => (int) $id,
                ':motivo' => $motivo,
                ':quantidade' => $quantidade,
                ':observacao' => trim((string) $observacao),
                ':usuario_id' => $usuarioId
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new RuntimeException('Erro ao registrar entrada.', 0, $e);
        }
    }

    public function registrarSaida($id, $motivo, $quantidade, $observacao = '', $usuarioId = null)
    {
        $motivosValidos = ['venda', 'consumo_interno', 'perda', 'avaria'];
        $quantidade = (int) $quantidade;
        $motivo = trim((string) $motivo);

        if ($quantidade <= 0 || !in_array($motivo, $motivosValidos, true)) {
            return false;
        }

        $produto = $this->buscarPorId($id);

        if (!$produto) {
            return false;
        }

        if ((int) $produto['quantidade'] < $quantidade) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $sqlProduto = "UPDATE produtos
                           SET quantidade = quantidade - :quantidade
                           WHERE id = :id
                             AND quantidade >= :quantidade";

            $stmtProduto = $this->conn->prepare($sqlProduto);
            $stmtProduto->execute([
                ':quantidade' => $quantidade,
                ':id' => (int) $id
            ]);

            if ($stmtProduto->rowCount() !== 1) {
                $this->conn->rollBack();
                return false;
            }

            $sqlMov = "INSERT INTO movimentacoes
                       (produto_id, tipo, motivo, quantidade, observacao, usuario_id)
                       VALUES
                       (:produto_id, 'saida', :motivo, :quantidade, :observacao, :usuario_id)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->execute([
                ':produto_id' => (int) $id,
                ':motivo' => $motivo,
                ':quantidade' => $quantidade,
                ':observacao' => trim((string) $observacao),
                ':usuario_id' => $usuarioId
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new RuntimeException('Erro ao registrar saída.', 0, $e);
        }
    }

    public function buscarHistoricoPorProduto($id)
    {
        $sql = "SELECT *
                FROM movimentacoes
                WHERE produto_id = :id
                ORDER BY data_hora DESC, id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMovimentacoes($produtoId = null, $limite = null): array
    {
        if ($limite !== null && (int) $limite <= 0) {
            return [];
        }

        $sql = "SELECT
                    movimentacoes.*,
                    produtos.nome AS produto_nome,
                    produtos.codigo AS produto_codigo,
                    produtos.unidade AS produto_unidade,
                    produtos.categoria AS produto_categoria
                FROM movimentacoes
                INNER JOIN produtos ON produtos.id = movimentacoes.produto_id";

        $params = [];

        if ($produtoId !== null && $produtoId !== '') {
            $sql .= " WHERE movimentacoes.produto_id = :produto_id";
            $params[':produto_id'] = (int) $produtoId;
        }

        $sql .= " ORDER BY movimentacoes.data_hora DESC, movimentacoes.id DESC";

        if ($limite !== null) {
            $sql .= " LIMIT :limite";
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor, PDO::PARAM_INT);
        }

        if ($limite !== null) {
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarUltimasMovimentacoes($limite = 8): array
    {
        return $this->listarMovimentacoes(null, $limite);
    }

    public function buscarDivergencias(): array
    {
        $sql = "SELECT *
                FROM produtos
                WHERE status = 'ativo'
                  AND (
                      quantidade < estoque_minimo
                      OR (
                          estoque_maximo IS NOT NULL
                          AND quantidade > estoque_maximo
                      )
                  )
                ORDER BY nome ASC, id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
