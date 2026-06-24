<?php

require_once __DIR__ . '/../../config/Database.php';

class Dashboard
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function buscarResumoGeral(): array
    {
        try {
            $sql = "SELECT
                        COUNT(*) AS total_produtos,
                        COALESCE(SUM(quantidade), 0) AS total_unidades,
                        COALESCE(SUM(quantidade * preco), 0) AS valor_total_estoque,
                        COALESCE(SUM(CASE 
                            WHEN status = 'ativo' AND quantidade < estoque_minimo 
                            THEN 1 ELSE 0 
                        END), 0) AS produtos_criticos
                    FROM produtos";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total_produtos' => (int) ($dados['total_produtos'] ?? 0),
                'total_unidades' => (int) ($dados['total_unidades'] ?? 0),
                'valor_total_estoque' => (float) ($dados['valor_total_estoque'] ?? 0),
                'produtos_criticos' => (int) ($dados['produtos_criticos'] ?? 0),
            ];
        } catch (PDOException $e) {
            error_log('Erro ao buscar resumo geral do dashboard: ' . $e->getMessage());

            return [
                'total_produtos' => 0,
                'total_unidades' => 0,
                'valor_total_estoque' => 0,
                'produtos_criticos' => 0,
            ];
        }
    }

    public function buscarEntradasSaidasPeriodo(int $dias = 30): array
    {
        try {
            $sql = "SELECT
                        tipo,
                        COALESCE(SUM(quantidade), 0) AS total
                    FROM movimentacoes
                    WHERE data_hora >= DATE_SUB(NOW(), INTERVAL :dias DAY)
                    GROUP BY tipo";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = [
                'entrada' => 0,
                'saida' => 0,
            ];

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
                $tipo = $linha['tipo'] ?? '';

                if ($tipo === 'entrada' || $tipo === 'saida') {
                    $resultado[$tipo] = (int) $linha['total'];
                }
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log('Erro ao buscar entradas e saídas do dashboard: ' . $e->getMessage());

            return [
                'entrada' => 0,
                'saida' => 0,
            ];
        }
    }

    /**
     * Compara as movimentações do período atual (últimos $dias) com o
     * período imediatamente anterior, para calcular tendências (▲▼).
     *
     * @return array{atual: array{entrada:int,saida:int}, anterior: array{entrada:int,saida:int}}
     */
    public function buscarComparativoMovimentacoes(int $dias = 30): array
    {
        $base = ['entrada' => 0, 'saida' => 0];
        $resultado = ['atual' => $base, 'anterior' => $base];

        try {
            $sql = "SELECT
                        CASE
                            WHEN data_hora >= DATE_SUB(NOW(), INTERVAL :dias_atual DAY)
                            THEN 'atual' ELSE 'anterior'
                        END AS periodo,
                        tipo,
                        COALESCE(SUM(quantidade), 0) AS total
                    FROM movimentacoes
                    WHERE data_hora >= DATE_SUB(NOW(), INTERVAL :dias_total DAY)
                    GROUP BY periodo, tipo";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':dias_atual', $dias, PDO::PARAM_INT);
            $stmt->bindValue(':dias_total', $dias * 2, PDO::PARAM_INT);
            $stmt->execute();

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
                $periodo = $linha['periodo'] ?? '';
                $tipo = $linha['tipo'] ?? '';
                if (isset($resultado[$periodo][$tipo])) {
                    $resultado[$periodo][$tipo] = (int) $linha['total'];
                }
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log('Erro ao buscar comparativo de movimentações: ' . $e->getMessage());
            return $resultado;
        }
    }

    public function buscarProdutosCriticos(int $limite = 5): array
    {
        try {
            $sql = "SELECT
                        id,
                        nome,
                        codigo,
                        categoria,
                        quantidade,
                        estoque_minimo
                    FROM produtos
                    WHERE status = 'ativo'
                      AND quantidade < estoque_minimo
                    ORDER BY quantidade ASC, nome ASC
                    LIMIT :limite";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro ao buscar produtos críticos do dashboard: ' . $e->getMessage());

            return [];
        }
    }

    public function buscarMaisMovimentados(int $limite = 5): array
    {
        try {
            $sql = "SELECT
                        p.id,
                        p.nome,
                        p.codigo,
                        COALESCE(SUM(m.quantidade), 0) AS total_movimentado
                    FROM produtos p
                    LEFT JOIN movimentacoes m ON m.produto_id = p.id
                    GROUP BY p.id, p.nome, p.codigo
                    ORDER BY total_movimentado DESC, p.nome ASC
                    LIMIT :limite";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro ao buscar produtos mais movimentados do dashboard: ' . $e->getMessage());

            return [];
        }
    }

    public function buscarTendenciaMovimentacoes(int $dias = 7): array
    {
        try {
            $sql = "SELECT
                        DATE(data_hora) AS data,
                        tipo,
                        COALESCE(SUM(quantidade), 0) AS total
                    FROM movimentacoes
                    WHERE data_hora >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                    GROUP BY DATE(data_hora), tipo
                    ORDER BY data ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro ao buscar tendência de movimentações do dashboard: ' . $e->getMessage());

            return [];
        }
    }
}