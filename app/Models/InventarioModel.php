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
    // ====================== CONTAGEM ======================
    
    /**
     * Salva múltiplas contagens de uma vez (usado pelo Controller)
     */
    public function salvarContagens($inventarioId, array $quantidades): bool
    {
        if (empty($quantidades)) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            foreach ($quantidades as $itemId => $quantidadeContada) {
                $itemId = (int) $itemId;
                $quantidadeContada = trim($quantidadeContada);

                $erros = self::validarContagem($quantidadeContada);
                if ($erros !== []) {
                    $this->conn->rollBack();
                    return false;
                }

                $sqlBuscar = "SELECT quantidade_sistema FROM inventario_itens WHERE id = :id";
                $stmtBuscar = $this->conn->prepare($sqlBuscar);
                $stmtBuscar->execute([':id' => $itemId]);
                $item = $stmtBuscar->fetch(PDO::FETCH_ASSOC);

                if (!$item) {
                    $this->conn->rollBack();
                    return false;
                }

                $quantidadeSistema = (int) $item['quantidade_sistema'];
                $diferenca = self::calcularDiferenca($quantidadeContada, $quantidadeSistema);

                $sqlUpdate = "
                    UPDATE inventario_itens 
                    SET quantidade_contada = :quantidade_contada,
                        diferenca = :diferenca
                    WHERE id = :id";

                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':quantidade_contada' => $quantidadeContada,
                    ':diferenca' => $diferenca,
                    ':id' => $itemId
                ]);
            }

            // Atualiza status do inventário para em_conferencia
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
        try {
            $this->conn->beginTransaction();

            $itens = $this->listarItens($inventarioId);

            foreach ($itens as $item) {
                // Só atualiza itens que tiveram contagem
                if ($item['quantidade_contada'] === null || $item['quantidade_contada'] === '') {
                    continue;
                }

                // Atualiza estoque do produto
                $sqlProduto = "UPDATE produtos SET quantidade = :quantidade WHERE id = :produto_id";
                $stmtProduto = $this->conn->prepare($sqlProduto);
                $stmtProduto->execute([
                    ':quantidade' => (int) $item['quantidade_contada'],
                    ':produto_id' => (int) $item['produto_id']
                ]);

                // Registra auditoria
                $sqlAuditoria = "
                    INSERT INTO auditorias_estoque (
                        inventario_id, produto_id, usuario_id,
                        quantidade_anterior, quantidade_nova, diferenca, motivo
                    ) VALUES (
                        :inventario_id, :produto_id, :usuario_id,
                        :quantidade_anterior, :quantidade_nova, :diferenca, :motivo
                    )";

                $stmtAuditoria = $this->conn->prepare($sqlAuditoria);
                $stmtAuditoria->execute([
                    ':inventario_id'     => $inventarioId,
                    ':produto_id'        => $item['produto_id'],
                    ':usuario_id'        => $adminId,
                    ':quantidade_anterior' => $item['quantidade_sistema'],
                    ':quantidade_nova'   => $item['quantidade_contada'],
                    ':diferenca'         => $item['diferenca'],
                    ':motivo'            => 'Ajuste aprovado via inventário'
                ]);
            }

            // Finaliza inventário
            $sqlFinalizar = "
                UPDATE inventarios 
                SET status = 'aprovado',
                    aprovado_por = :admin_id,
                    finalizado_em = NOW()
                WHERE id = :id";

            $stmtFinalizar = $this->conn->prepare($sqlFinalizar);
            $stmtFinalizar->execute([
                ':admin_id' => $adminId,
                ':id'       => $inventarioId
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