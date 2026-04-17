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

    public function listarFiltrados($busca = '', $categoria = '', $unidade = '', $status = '')
    {
        $sql = "SELECT * FROM produtos WHERE 1=1";
        $params = [];

        $busca = trim((string) $busca);
        $categoria = trim((string) $categoria);
        $unidade = trim((string) $unidade);
        $status = trim((string) $status);

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

    public function criar($dados)
    {
        try {
            $sql = "INSERT INTO produtos
                    (nome, codigo, categoria, unidade, descricao, status, quantidade, preco)
                    VALUES
                    (:nome, :codigo, :categoria, :unidade, :descricao, :status, :quantidade, :preco)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nome' => trim((string) ($dados['nome'] ?? '')),
                ':codigo' => $this->valorOuNull($dados['codigo'] ?? null),
                ':categoria' => $this->valorOuNull($dados['categoria'] ?? null),
                ':unidade' => $this->valorOuNull($dados['unidade'] ?? null),
                ':descricao' => $this->valorOuNull($dados['descricao'] ?? null),
                ':status' => trim((string) ($dados['status'] ?? 'ativo')),
                ':quantidade' => (int) ($dados['quantidade'] ?? 0),
                ':preco' => (float) ($dados['preco'] ?? 0)
            ]);
        } catch (PDOException $e) {
            die('Erro ao criar produto: ' . $e->getMessage());
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
                        preco = :preco
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id' => (int) $id,
                ':nome' => trim((string) ($dados['nome'] ?? '')),
                ':codigo' => $this->valorOuNull($dados['codigo'] ?? null),
                ':categoria' => $this->valorOuNull($dados['categoria'] ?? null),
                ':unidade' => $this->valorOuNull($dados['unidade'] ?? null),
                ':descricao' => $this->valorOuNull($dados['descricao'] ?? null),
                ':status' => trim((string) ($dados['status'] ?? 'ativo')),
                ':quantidade' => (int) ($dados['quantidade'] ?? 0),
                ':preco' => (float) ($dados['preco'] ?? 0)
            ]);
        } catch (PDOException $e) {
            die('Erro ao atualizar produto: ' . $e->getMessage());
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
            die('Erro ao excluir produto: ' . $e->getMessage());
        }
    }

    public function movimentar($id, $tipo, $quantidade, $observacao = '')
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
                               WHERE id = :id";
            }

            $stmtProduto = $this->conn->prepare($sqlProduto);
            $stmtProduto->execute([
                ':quantidade' => $quantidade,
                ':id' => (int) $id
            ]);

            $sqlMov = "INSERT INTO movimentacoes
                       (produto_id, tipo, motivo, quantidade, observacao)
                       VALUES
                       (:produto_id, :tipo, :motivo, :quantidade, :observacao)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->execute([
                ':produto_id' => (int) $id,
                ':tipo' => $tipo,
                ':motivo' => $motivo,
                ':quantidade' => $quantidade,
                ':observacao' => trim((string) $observacao)
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            die('Erro ao movimentar produto: ' . $e->getMessage());
        }
    }

    public function registrarEntrada($id, $motivo, $quantidade, $observacao = '')
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

            $sqlMov = "INSERT INTO movimentacoes
                       (produto_id, tipo, motivo, quantidade, observacao)
                       VALUES
                       (:produto_id, 'entrada', :motivo, :quantidade, :observacao)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->execute([
                ':produto_id' => (int) $id,
                ':motivo' => $motivo,
                ':quantidade' => $quantidade,
                ':observacao' => trim((string) $observacao)
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            die('Erro ao registrar entrada: ' . $e->getMessage());
        }
    }

    public function registrarSaida($id, $motivo, $quantidade, $observacao = '')
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
                           WHERE id = :id";

            $stmtProduto = $this->conn->prepare($sqlProduto);
            $stmtProduto->execute([
                ':quantidade' => $quantidade,
                ':id' => (int) $id
            ]);

            $sqlMov = "INSERT INTO movimentacoes
                       (produto_id, tipo, motivo, quantidade, observacao)
                       VALUES
                       (:produto_id, 'saida', :motivo, :quantidade, :observacao)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->execute([
                ':produto_id' => (int) $id,
                ':motivo' => $motivo,
                ':quantidade' => $quantidade,
                ':observacao' => trim((string) $observacao)
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            die('Erro ao registrar saída: ' . $e->getMessage());
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
}