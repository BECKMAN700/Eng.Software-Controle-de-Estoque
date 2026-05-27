<?php

require_once __DIR__ . '/../../config/Database.php';

class InventarioModel
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

    public static function calcularDiferenca($quantidadeContada, $quantidadeSistema): ?int
    {
        if ($quantidadeContada === null || $quantidadeContada === '') {
            return null;
        }

        return (int) $quantidadeContada - (int) $quantidadeSistema;
    }

    public static function validarContagem($quantidadeContada): array
    {
        if ($quantidadeContada === null || $quantidadeContada === '') {
            return [];
        }

        if (filter_var($quantidadeContada, FILTER_VALIDATE_INT) === false) {
            return ['quantidade_contada' => 'A quantidade contada deve ser um numero inteiro.'];
        }

        if ((int) $quantidadeContada < 0) {
            return ['quantidade_contada' => 'A quantidade contada nao pode ser negativa.'];
        }

        return [];
    }

    public function abrir(array $dados)
    {
        $titulo = trim((string) ($dados['titulo'] ?? ''));
        $criadoPor = (int) ($dados['criado_por'] ?? 0);
        $categoria = $this->valorOuNull($dados['categoria'] ?? null);
        $observacao = $this->valorOuNull($dados['observacao'] ?? null);

        if ($titulo === '' || $criadoPor <= 0) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $produtos = $this->buscarProdutosParaInventario($categoria);

            if ($produtos === []) {
                $this->conn->rollBack();
                return false;
            }

            $sqlInventario = "INSERT INTO inventarios
                    (titulo, categoria, status, criado_por, observacao)
                    VALUES
                    (:titulo, :categoria, 'aberto', :criado_por, :observacao)";

            $stmtInventario = $this->conn->prepare($sqlInventario);
            $stmtInventario->execute([
                ':titulo' => $titulo,
                ':categoria' => $categoria,
                ':criado_por' => $criadoPor,
                ':observacao' => $observacao,
            ]);

            $inventarioId = (int) $this->conn->lastInsertId();

            $sqlItem = "INSERT INTO inventario_itens
                    (inventario_id, produto_id, quantidade_sistema)
                    VALUES
                    (:inventario_id, :produto_id, :quantidade_sistema)";

            $stmtItem = $this->conn->prepare($sqlItem);

            foreach ($produtos as $produto) {
                $stmtItem->execute([
                    ':inventario_id' => $inventarioId,
                    ':produto_id' => (int) $produto['id'],
                    ':quantidade_sistema' => (int) $produto['quantidade'],
                ]);
            }

            $this->conn->commit();
            return $inventarioId;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw new RuntimeException('Erro ao abrir inventario.', 0, $e);
        }
    }

    public function contarProdutosAtivos($categoria = null): int
    {
        $categoria = $this->valorOuNull($categoria);
        $sql = "SELECT COUNT(*) FROM produtos WHERE status = 'ativo'";
        $params = [];

        if ($categoria !== null) {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function buscarProdutosParaInventario($categoria): array
    {
        $sql = "SELECT id, quantidade
                FROM produtos
                WHERE status = 'ativo'";
        $params = [];

        if ($categoria !== null) {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $sql .= " ORDER BY nome ASC, id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar(): array
    {
        $sql = "SELECT
                    inventarios.*,
                    criador.nome AS criado_por_nome,
                    aprovador.nome AS aprovado_por_nome,
                    (
                        SELECT COUNT(*)
                        FROM inventario_itens
                        WHERE inventario_itens.inventario_id = inventarios.id
                    ) AS total_itens,
                    (
                        SELECT COUNT(*)
                        FROM inventario_itens
                        WHERE inventario_itens.inventario_id = inventarios.id
                          AND inventario_itens.diferenca IS NOT NULL
                          AND inventario_itens.diferenca <> 0
                    ) AS total_divergencias
                FROM inventarios
                INNER JOIN usuarios criador ON criador.id = inventarios.criado_por
                LEFT JOIN usuarios aprovador ON aprovador.id = inventarios.aprovado_por
                ORDER BY inventarios.criado_em DESC, inventarios.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT
                    inventarios.*,
                    criador.nome AS criado_por_nome,
                    aprovador.nome AS aprovado_por_nome
                FROM inventarios
                INNER JOIN usuarios criador ON criador.id = inventarios.criado_por
                LEFT JOIN usuarios aprovador ON aprovador.id = inventarios.aprovado_por
                WHERE inventarios.id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        $inventario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inventario) {
            return null;
        }

        $inventario['itens'] = $this->listarItens($id);

        return $inventario;
    }

    public function listarItens($inventarioId): array
    {
        $sql = "SELECT
                    inventario_itens.*,
                    produtos.nome AS produto_nome,
                    produtos.codigo AS produto_codigo,
                    produtos.categoria AS produto_categoria,
                    produtos.unidade AS produto_unidade
                FROM inventario_itens
                INNER JOIN produtos ON produtos.id = inventario_itens.produto_id
                WHERE inventario_itens.inventario_id = :inventario_id
                ORDER BY produtos.nome ASC, inventario_itens.id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':inventario_id', (int) $inventarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAuditoria($inventarioId): array
    {
        $sql = "SELECT
                    auditorias_estoque.*,
                    produtos.nome AS produto_nome,
                    produtos.codigo AS produto_codigo,
                    usuarios.nome AS usuario_nome
                FROM auditorias_estoque
                INNER JOIN produtos ON produtos.id = auditorias_estoque.produto_id
                INNER JOIN usuarios ON usuarios.id = auditorias_estoque.usuario_id
                WHERE auditorias_estoque.inventario_id = :inventario_id
                ORDER BY auditorias_estoque.criado_em DESC, auditorias_estoque.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':inventario_id', (int) $inventarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function temContagensPendentes($inventarioId): bool
    {
        $sql = "SELECT COUNT(*)
                FROM inventario_itens
                WHERE inventario_id = :inventario_id
                  AND quantidade_contada IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':inventario_id', (int) $inventarioId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function salvarContagens($inventarioId, array $quantidades, array $observacoes = []): bool
    {
        $inventarioId = (int) $inventarioId;

        if ($inventarioId <= 0 || empty($quantidades)) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $sqlInventario = "SELECT status FROM inventarios WHERE id = :id FOR UPDATE";
            $stmtInventario = $this->conn->prepare($sqlInventario);
            $stmtInventario->execute([':id' => $inventarioId]);
            $inventario = $stmtInventario->fetch(PDO::FETCH_ASSOC);

            if (!$inventario || !in_array($inventario['status'], ['aberto', 'em_conferencia'], true)) {
                $this->conn->rollBack();
                return false;
            }

            foreach ($quantidades as $itemId => $quantidadeContada) {
                $itemId = (int) $itemId;
                $quantidadeContada = trim((string) $quantidadeContada);
                $observacao = $this->valorOuNull($observacoes[$itemId] ?? null);

                $erros = self::validarContagem($quantidadeContada);
                if ($erros !== []) {
                    $this->conn->rollBack();
                    return false;
                }

                $sqlBuscar = "SELECT quantidade_sistema
                              FROM inventario_itens
                              WHERE id = :id
                                AND inventario_id = :inventario_id";
                $stmtBuscar = $this->conn->prepare($sqlBuscar);
                $stmtBuscar->execute([
                    ':id' => $itemId,
                    ':inventario_id' => $inventarioId,
                ]);
                $item = $stmtBuscar->fetch(PDO::FETCH_ASSOC);

                if (!$item) {
                    $this->conn->rollBack();
                    return false;
                }

                $diferenca = self::calcularDiferenca($quantidadeContada, (int) $item['quantidade_sistema']);

                $sqlUpdate = "UPDATE inventario_itens
                              SET quantidade_contada = :quantidade_contada,
                                  diferenca = :diferenca,
                                  observacao = :observacao
                              WHERE id = :id
                                AND inventario_id = :inventario_id";

                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':quantidade_contada' => $quantidadeContada === '' ? null : (int) $quantidadeContada,
                    ':diferenca' => $diferenca,
                    ':observacao' => $observacao,
                    ':id' => $itemId,
                    ':inventario_id' => $inventarioId,
                ]);
            }

            $sqlStatus = "UPDATE inventarios SET status = 'em_conferencia' WHERE id = :id";
            $stmtStatus = $this->conn->prepare($sqlStatus);
            $stmtStatus->execute([':id' => $inventarioId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function aprovarInventario($inventarioId, $adminId): bool
    {
        $inventarioId = (int) $inventarioId;
        $adminId = (int) $adminId;

        if ($inventarioId <= 0 || $adminId <= 0) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $sqlInventario = "SELECT status FROM inventarios WHERE id = :id FOR UPDATE";
            $stmtInventario = $this->conn->prepare($sqlInventario);
            $stmtInventario->execute([':id' => $inventarioId]);
            $inventario = $stmtInventario->fetch(PDO::FETCH_ASSOC);

            if (!$inventario || $inventario['status'] !== 'em_conferencia') {
                $this->conn->rollBack();
                return false;
            }

            $itens = $this->listarItens($inventarioId);

            if ($itens === [] || $this->temContagensPendentes($inventarioId)) {
                $this->conn->rollBack();
                return false;
            }

            foreach ($itens as $item) {
                $sqlProduto = "UPDATE produtos SET quantidade = :quantidade WHERE id = :produto_id";
                $stmtProduto = $this->conn->prepare($sqlProduto);
                $stmtProduto->execute([
                    ':quantidade' => (int) $item['quantidade_contada'],
                    ':produto_id' => (int) $item['produto_id'],
                ]);

                $sqlAuditoria = "INSERT INTO auditorias_estoque (
                                    inventario_id, produto_id, usuario_id,
                                    quantidade_anterior, quantidade_nova, diferenca, motivo
                                ) VALUES (
                                    :inventario_id, :produto_id, :usuario_id,
                                    :quantidade_anterior, :quantidade_nova, :diferenca, :motivo
                                )";

                $stmtAuditoria = $this->conn->prepare($sqlAuditoria);
                $stmtAuditoria->execute([
                    ':inventario_id' => $inventarioId,
                    ':produto_id' => (int) $item['produto_id'],
                    ':usuario_id' => $adminId,
                    ':quantidade_anterior' => (int) $item['quantidade_sistema'],
                    ':quantidade_nova' => (int) $item['quantidade_contada'],
                    ':diferenca' => (int) $item['diferenca'],
                    ':motivo' => 'Ajuste aprovado via inventario',
                ]);
            }

            $sqlFinalizar = "UPDATE inventarios
                             SET status = 'aprovado',
                                 aprovado_por = :admin_id,
                                 finalizado_em = NOW()
                             WHERE id = :id";

            $stmtFinalizar = $this->conn->prepare($sqlFinalizar);
            $stmtFinalizar->execute([
                ':admin_id' => $adminId,
                ':id' => $inventarioId,
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }
}
