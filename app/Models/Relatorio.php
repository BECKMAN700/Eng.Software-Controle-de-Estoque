<?php

require_once __DIR__ . '/../../config/Database.php';

class Relatorio
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conectar();
        $this->verificarEAdicionarColunaUsuario();
    }

    /**
     * Adiciona de forma segura a coluna usuario_id à tabela movimentacoes se não existir.
     */
    private function verificarEAdicionarColunaUsuario(): void
    {
        try {
            $this->conn->query("SELECT usuario_id FROM movimentacoes LIMIT 1");
        } catch (PDOException $e) {
            try {
                $this->conn->exec("ALTER TABLE movimentacoes ADD COLUMN usuario_id INT NULL");
                $this->conn->exec("ALTER TABLE movimentacoes ADD CONSTRAINT fk_movimentacoes_usuario 
                                  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL");
            } catch (PDOException $ex) {
                // Silencia se outra requisição ou thread concorrente já realizou a alteração
            }
        }
    }

    /**
     * Relatório de Giro de Estoque
     */
    public function buscarGiroEstoque($busca = '', $categoria = '', $dataInicial = '', $dataFinal = ''): array
    {
        try {
            $sql = "SELECT
                        p.id,
                        p.nome,
                        p.codigo,
                        p.categoria,
                        COALESCE(SUM(CASE WHEN m.tipo = 'entrada' THEN m.quantidade ELSE 0 END), 0) AS total_entradas,
                        COALESCE(SUM(CASE WHEN m.tipo = 'saida' THEN m.quantidade ELSE 0 END), 0) AS total_saidas,
                        (COALESCE(SUM(CASE WHEN m.tipo = 'entrada' THEN m.quantidade ELSE 0 END), 0) +
                         COALESCE(SUM(CASE WHEN m.tipo = 'saida' THEN m.quantidade ELSE 0 END), 0)) AS total_movimentado,
                        MAX(m.data_hora) AS ultima_movimentacao
                    FROM produtos p
                    LEFT JOIN movimentacoes m ON p.id = m.produto_id
                    WHERE 1=1";
            
            $params = [];
            $busca = trim((string) $busca);
            $categoria = trim((string) $categoria);
            $dataInicial = trim((string) $dataInicial);
            $dataFinal = trim((string) $dataFinal);

            if ($busca !== '') {
                $sql .= " AND (p.nome LIKE :busca OR p.codigo LIKE :busca)";
                $params[':busca'] = '%' . $busca . '%';
            }

            if ($categoria !== '') {
                $sql .= " AND p.categoria = :categoria";
                $params[':categoria'] = $categoria;
            }

            if ($dataInicial !== '' && $dataFinal !== '') {
                $sql .= " AND m.data_hora BETWEEN :data_inicial AND :data_final";
                $params[':data_inicial'] = $dataInicial . ' 00:00:00';
                $params[':data_final'] = $dataFinal . ' 23:59:59';
            }

            $sql .= " GROUP BY p.id, p.nome, p.codigo, p.categoria
                      ORDER BY total_movimentado DESC, p.nome ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro no Relatório de Giro de Estoque: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Relatório de Valorização do Estoque
     */
    public function buscarValorizacaoEstoque($categoria = ''): array
    {
        try {
            $sql = "SELECT
                        id,
                        nome,
                        codigo,
                        quantidade,
                        preco,
                        categoria,
                        (quantidade * preco) AS valor_total_produto
                    FROM produtos
                    WHERE status = 'ativo'";

            $params = [];
            $categoria = trim((string) $categoria);
            if ($categoria !== '') {
                $sql .= " AND categoria = :categoria";
                $params[':categoria'] = $categoria;
            }

            $sql .= " ORDER BY valor_total_produto DESC, nome ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro no Relatório de Valorização: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Relatório de Movimentações por Período
     */
    public function buscarMovimentacoesPorPeriodo($dataInicial, $dataFinal, $tipo = 'todos', $produtoId = '', $categoria = ''): array
    {
        try {
            $sql = "SELECT
                        m.data_hora,
                        m.tipo,
                        m.quantidade,
                        m.motivo,
                        m.observacao,
                        p.nome AS produto_nome,
                        p.codigo AS produto_codigo,
                        p.categoria AS produto_categoria,
                        u.nome AS usuario_nome
                    FROM movimentacoes m
                    INNER JOIN produtos p ON p.id = m.produto_id
                    LEFT JOIN usuarios u ON u.id = m.usuario_id
                    WHERE m.data_hora BETWEEN :data_inicial AND :data_final";

            $params = [
                ':data_inicial' => $dataInicial . ' 00:00:00',
                ':data_final' => $dataFinal . ' 23:59:59'
            ];

            $tipo = trim((string) $tipo);
            if ($tipo === 'entrada' || $tipo === 'saida') {
                $sql .= " AND m.tipo = :tipo";
                $params[':tipo'] = $tipo;
            }

            $produtoId = trim((string) $produtoId);
            if ($produtoId !== '') {
                $sql .= " AND m.produto_id = :produto_id";
                $params[':produto_id'] = (int) $produtoId;
            }

            $categoria = trim((string) $categoria);
            if ($categoria !== '') {
                $sql .= " AND p.categoria = :categoria";
                $params[':categoria'] = $categoria;
            }

            $sql .= " ORDER BY m.data_hora DESC, m.id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro no Relatório de Movimentações por Período: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar Categorias dos Produtos
     */
    public function listarCategorias(): array
    {
        try {
            $sql = "SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL AND TRIM(categoria) <> '' ORDER BY categoria ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Listar Produtos
     */
    public function listarProdutos(): array
    {
        try {
            $sql = "SELECT id, nome, codigo FROM produtos ORDER BY nome ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
