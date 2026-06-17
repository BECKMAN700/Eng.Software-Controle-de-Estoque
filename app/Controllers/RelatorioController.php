    <?php

    require_once __DIR__ . '/../Helpers/Auth.php';
    require_once __DIR__ . '/../Helpers/Sessao.php';
    require_once __DIR__ . '/../Helpers/Validacao.php';
    require_once __DIR__ . '/../Models/Relatorio.php';
    require_once __DIR__ . '/../Helpers/fpdf.php';

    class RelatorioController
    {
        private $model;

        public function __construct()
        {
            Auth::exigirLogin();
            $this->model = new Relatorio();
        }

        private function conv(string $str): string
        {
            if (function_exists('iconv')) {
                return @iconv('UTF-8', 'windows-1252//TRANSLIT', $str) ?: $str;
            }
            // Fallback sem utf8_decode (descontinuado a partir do PHP 8.2)
            return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
        }

        /**
         * Classifica o giro de um produto a partir do total movimentado.
         * Centraliza a regra usada na tela e nas exportacoes (PDF e CSV).
         */
        private function classificarGiro(int $totalMovimentado): string
        {
            if ($totalMovimentado >= 50) {
                return 'alto';
            }
            if ($totalMovimentado > 10) {
                return 'medio';
            }
            return 'baixo';
        }

        /**
         * Página inicial de Relatórios
         */
        public function index(): void
        {
            include __DIR__ . '/../Views/relatorios/index.php';
        }

        /**
         * Relatório de Giro de Estoque
         */
        public function giroEstoque(): void
        {
            $busca = $_GET['busca'] ?? '';
            $categoria = $_GET['categoria'] ?? '';
            $dataInicial = $_GET['data_inicial'] ?? '';
            $dataFinal = $_GET['data_final'] ?? '';
            $situacao = $_GET['situacao'] ?? ''; // 'alto', 'medio', 'baixo'
            $categorias = $this->model->listarCategorias();
            $erros = [];
            $filtroPeriodoAtivo = $dataInicial !== '' || $dataFinal !== '';

            if ($filtroPeriodoAtivo) {
                $erros = Validacao::periodoRelatorio([
                    'data_inicial' => $dataInicial,
                    'data_final' => $dataFinal
                ]);
            }

            $dadosGiro = empty($erros)
                ? $this->model->buscarGiroEstoque($busca, $categoria, $dataInicial, $dataFinal)
                : [];

            // Classifica os itens na situação de giro
            $dadosGiro = array_map(function ($item) {
                $item['situacao'] = $this->classificarGiro((int) $item['total_movimentado']);
                return $item;
            }, $dadosGiro);

            // Filtra por situação se especificado
            if ($situacao !== '' && empty($erros)) {
                $dadosGiro = array_filter($dadosGiro, function ($item) use ($situacao) {
                    return $item['situacao'] === $situacao;
                });
            }

            include __DIR__ . '/../Views/relatorios/giro_estoque.php';
        }

        /**
         * Relatório de Valorização do Estoque
         */
        public function valorizacao(): void
        {
            $categoria = $_GET['categoria'] ?? '';
            $dadosValorizacao = $this->model->buscarValorizacaoEstoque($categoria);
            $categorias = $this->model->listarCategorias();

            $valorTotalGeral = 0;
            $totalItens = 0;
            foreach ($dadosValorizacao as $item) {
                $valorTotalGeral += (float) $item['valor_total_produto'];
                $totalItens += (int) $item['quantidade'];
            }

            include __DIR__ . '/../Views/relatorios/valorizacao.php';
        }

        /**
         * Relatório de Movimentações por Período
         */
        public function movimentacoesPeriodo(): void
        {
            $dataInicial = $_GET['data_inicial'] ?? '';
            $dataFinal = $_GET['data_final'] ?? '';
            $tipo = $_GET['tipo'] ?? 'todos';
            $produtoId = $_GET['produto_id'] ?? '';
            $categoria = $_GET['categoria'] ?? '';

            $erros = [];
            $movimentacoes = [];
            $totais = ['entrada' => 0, 'saida' => 0];

            $categorias = $this->model->listarCategorias();
            $produtos = $this->model->listarProdutos();

            $realizarConsulta = false;

            // Se o usuário clicou em filtrar ou informou alguma data
            if (isset($_GET['filtrar']) || !empty($dataInicial) || !empty($dataFinal)) {
                $erros = Validacao::periodoRelatorio([
                    'data_inicial' => $dataInicial,
                    'data_final' => $dataFinal
                ]);

                if (empty($erros)) {
                    $realizarConsulta = true;
                    $movimentacoes = $this->model->buscarMovimentacoesPorPeriodo($dataInicial, $dataFinal, $tipo, $produtoId, $categoria);
                    
                    foreach ($movimentacoes as $mov) {
                        if ($mov['tipo'] === 'entrada') {
                            $totais['entrada'] += (int) $mov['quantidade'];
                        } elseif ($mov['tipo'] === 'saida') {
                            $totais['saida'] += (int) $mov['quantidade'];
                        }
                    }
                }
            }

            include __DIR__ . '/../Views/relatorios/movimentacoes_periodo.php';
        }

        /**
         * Exportação para CSV
         */
        public function exportarCsv(): void
        {
            $relatorio = $_GET['relatorio'] ?? '';
            
            if ($relatorio === 'giro_estoque') {
                $busca = $_GET['busca'] ?? '';
                $categoria = $_GET['categoria'] ?? '';
                $dataInicial = $_GET['data_inicial'] ?? '';
                $dataFinal = $_GET['data_final'] ?? '';
                $situacao = $_GET['situacao'] ?? '';
                $erros = [];
                
                if ($dataInicial !== '' || $dataFinal !== '') {
                    $erros = Validacao::periodoRelatorio([
                        'data_inicial' => $dataInicial,
                        'data_final' => $dataFinal
                    ]);
                }

                if ($erros !== []) {
                    echo 'Filtros de periodo invalidos para exportacao.';
                    exit;
                }

                $dados = $this->model->buscarGiroEstoque($busca, $categoria, $dataInicial, $dataFinal);
                $dados = array_map(function ($item) {
                    $item['situacao'] = $this->classificarGiro((int) $item['total_movimentado']);
                    return $item;
                }, $dados);

                if ($situacao !== '') {
                    $dados = array_filter($dados, function ($item) use ($situacao) {
                        return $item['situacao'] === $situacao;
                    });
                }

                $filename = 'relatorio_giro_estoque_' . date('Y-m-d_H-i') . '.csv';

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                // Bom UTF-8 para Excel
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($output, ['Produto', 'Código', 'Categoria', 'Total Entradas', 'Total Saídas', 'Total Movimentado', 'Última Movimentação', 'Giro'], ';');

                foreach ($dados as $row) {
                    $giroLabel = 'Baixo';
                    if ($row['situacao'] === 'alto') {
                        $giroLabel = 'Alto';
                    } elseif ($row['situacao'] === 'medio') {
                        $giroLabel = 'Médio';
                    }
                    fputcsv($output, [
                        $row['nome'],
                        $row['codigo'] ?: '-',
                        $row['categoria'] ?: '-',
                        $row['total_entradas'],
                        $row['total_saidas'],
                        $row['total_movimentado'],
                        $row['ultima_movimentacao'] ? date('d/m/Y H:i', strtotime($row['ultima_movimentacao'])) : '-',
                        $giroLabel
                    ], ';');
                }
                fclose($output);
                exit;

            } elseif ($relatorio === 'valorizacao') {
                $categoria = $_GET['categoria'] ?? '';
                $dados = $this->model->buscarValorizacaoEstoque($categoria);

                $filename = 'relatorio_valorizacao_' . date('Y-m-d_H-i') . '.csv';

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                $output = fopen('php://output', 'w');
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($output, ['Produto', 'Código', 'Categoria', 'Quantidade Atual', 'Preço Unitário (R$)', 'Valor Total (R$)'], ';');

                $totalGeral = 0;
                foreach ($dados as $row) {
                    $totalGeral += (float) $row['valor_total_produto'];
                    fputcsv($output, [
                        $row['nome'],
                        $row['codigo'] ?: '-',
                        $row['categoria'] ?: '-',
                        $row['quantidade'],
                        number_format((float) $row['preco'], 2, ',', '.'),
                        number_format((float) $row['valor_total_produto'], 2, ',', '.')
                    ], ';');
                }

                fputcsv($output, [], ';');
                fputcsv($output, ['TOTAL GERAL DO ESTOQUE', '', '', '', '', 'R$ ' . number_format($totalGeral, 2, ',', '.')], ';');

                fclose($output);
                exit;

            } elseif ($relatorio === 'movimentacoes') {
                $dataInicial = $_GET['data_inicial'] ?? '';
                $dataFinal = $_GET['data_final'] ?? '';
                $tipo = $_GET['tipo'] ?? 'todos';
                $produtoId = $_GET['produto_id'] ?? '';
                $categoria = $_GET['categoria'] ?? '';

                $erros = Validacao::periodoRelatorio([
                    'data_inicial' => $dataInicial,
                    'data_final' => $dataFinal
                ]);

                if ($erros !== []) {
                    echo 'Filtros de data inicial e final sao obrigatorios e devem formar um periodo valido.';
                    exit;
                }

                $dados = $this->model->buscarMovimentacoesPorPeriodo($dataInicial, $dataFinal, $tipo, $produtoId, $categoria);

                $filename = 'relatorio_movimentacoes_' . $dataInicial . '_a_' . $dataFinal . '.csv';

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                $output = fopen('php://output', 'w');
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($output, ['Data/Hora', 'Produto', 'Código', 'Categoria', 'Tipo', 'Quantidade', 'Motivo', 'Responsável'], ';');

                $motivos = [
                    'compra' => 'Compra',
                    'devolucao' => 'Devolução',
                    'transferencia' => 'Transferência',
                    'venda' => 'Venda',
                    'consumo_interno' => 'Consumo interno',
                    'perda' => 'Perda',
                    'avaria' => 'Avaria',
                    'entrada_manual' => 'Entrada manual',
                    'saida_manual' => 'Saída manual'
                ];

                $totalEntradas = 0;
                $totalSaidas = 0;

                foreach ($dados as $row) {
                    if ($row['tipo'] === 'entrada') {
                        $totalEntradas += (int) $row['quantidade'];
                    } else {
                        $totalSaidas += (int) $row['quantidade'];
                    }

                    $motivoTraduzido = $motivos[$row['motivo']] ?? ucfirst(str_replace('_', ' ', $row['motivo']));

                    fputcsv($output, [
                        date('d/m/Y H:i', strtotime($row['data_hora'])),
                        $row['produto_nome'],
                        $row['produto_codigo'] ?: '-',
                        $row['produto_categoria'] ?: '-',
                        ucfirst($row['tipo']),
                        $row['quantidade'],
                        $motivoTraduzido,
                        $row['usuario_nome'] ?: '-'
                    ], ';');
                }

                fputcsv($output, [], ';');
                fputcsv($output, ['RESUMO DO PERÍODO'], ';');
                fputcsv($output, ['Total Entradas', $totalEntradas], ';');
                fputcsv($output, ['Total Saídas', $totalSaidas], ';');

                fclose($output);
                exit;
            }

            echo 'Relatório não especificado.';
            exit;
        }

        /**
         * Exportação para PDF
         */
        public function exportarPdf(): void
        {
            $relatorio = $_GET['relatorio'] ?? '';

            if ($relatorio === 'giro_estoque') {
                $busca = $_GET['busca'] ?? '';
                $categoria = $_GET['categoria'] ?? '';
                $dataInicial = $_GET['data_inicial'] ?? '';
                $dataFinal = $_GET['data_final'] ?? '';
                $situacao = $_GET['situacao'] ?? '';
                $erros = [];
                
                if ($dataInicial !== '' || $dataFinal !== '') {
                    $erros = Validacao::periodoRelatorio([
                        'data_inicial' => $dataInicial,
                        'data_final' => $dataFinal
                    ]);
                }

                if ($erros !== []) {
                    echo 'Filtros de periodo invalidos para exportacao.';
                    exit;
                }

                $dados = $this->model->buscarGiroEstoque($busca, $categoria, $dataInicial, $dataFinal);
                $dados = array_map(function ($item) {
                    $item['situacao'] = $this->classificarGiro((int) $item['total_movimentado']);
                    return $item;
                }, $dados);

                if ($situacao !== '') {
                    $dados = array_filter($dados, function ($item) use ($situacao) {
                        return strtolower($item['situacao']) === strtolower($situacao);
                    });
                }

                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0, 10, $this->conv('Relatório de Giro de Estoque'), 0, 1, 'C');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 8, $this->conv('Gerado em: ' . date('d/m/Y H:i')), 0, 1, 'C');
                $pdf->Ln(5);

                // Table Headers
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(65, 8, $this->conv('Produto'), 1, 0, 'L', true);
                $pdf->Cell(25, 8, $this->conv('Cod'), 1, 0, 'C', true);
                $pdf->Cell(25, 8, $this->conv('Entradas'), 1, 0, 'C', true);
                $pdf->Cell(25, 8, $this->conv('Saidas'), 1, 0, 'C', true);
                $pdf->Cell(25, 8, $this->conv('Total Mov.'), 1, 0, 'C', true);
                $pdf->Cell(25, 8, $this->conv('Giro'), 1, 1, 'C', true);

                $pdf->SetFont('Arial', '', 9);
                $fill = false;
                foreach ($dados as $row) {
                    $pdf->Cell(65, 7, $this->conv($row['nome']), 1, 0, 'L', $fill);
                    $pdf->Cell(25, 7, $this->conv($row['codigo'] ?: '-'), 1, 0, 'C', $fill);
                    $pdf->Cell(25, 7, $row['total_entradas'], 1, 0, 'C', $fill);
                    $pdf->Cell(25, 7, $row['total_saidas'], 1, 0, 'C', $fill);
                    $pdf->Cell(25, 7, $row['total_movimentado'], 1, 0, 'C', $fill);
                    $pdf->Cell(25, 7, $this->conv(ucfirst($row['situacao'])), 1, 1, 'C', $fill);
                    $fill = !$fill;
                }

                $pdf->Output('I', 'relatorio_giro_estoque_' . date('Y-m-d') . '.pdf');
                exit;

            } elseif ($relatorio === 'valorizacao') {
                $categoria = $_GET['categoria'] ?? '';
                $dados = $this->model->buscarValorizacaoEstoque($categoria);

                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0, 10, $this->conv('Relatório de Valorização do Estoque'), 0, 1, 'C');
                $pdf->SetFont('Arial', '', 10);
                if (!empty($categoria)) {
                    $pdf->Cell(0, 6, $this->conv('Categoria: ' . $categoria), 0, 1, 'C');
                }
                $pdf->Cell(0, 6, $this->conv('Gerado em: ' . date('d/m/Y H:i')), 0, 1, 'C');
                $pdf->Ln(5);

                // Table Headers
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(70, 8, $this->conv('Produto'), 1, 0, 'L', true);
                $pdf->Cell(25, 8, $this->conv('Cod'), 1, 0, 'C', true);
                $pdf->Cell(30, 8, $this->conv('Qtd Atual'), 1, 0, 'C', true);
                $pdf->Cell(30, 8, $this->conv('Vlr Unitario'), 1, 0, 'R', true);
                $pdf->Cell(35, 8, $this->conv('Valor Total'), 1, 1, 'R', true);

                $pdf->SetFont('Arial', '', 9);
                $fill = false;
                $totalGeral = 0;
                foreach ($dados as $row) {
                    $totalGeral += (float) $row['valor_total_produto'];
                    $pdf->Cell(70, 7, $this->conv($row['nome']), 1, 0, 'L', $fill);
                    $pdf->Cell(25, 7, $this->conv($row['codigo'] ?: '-'), 1, 0, 'C', $fill);
                    $pdf->Cell(30, 7, $row['quantidade'], 1, 0, 'C', $fill);
                    $pdf->Cell(30, 7, 'R$ ' . number_format((float) $row['preco'], 2, ',', '.'), 1, 0, 'R', $fill);
                    $pdf->Cell(35, 7, 'R$ ' . number_format((float) $row['valor_total_produto'], 2, ',', '.'), 1, 1, 'R', $fill);
                    $fill = !$fill;
                }

                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(125, 8, $this->conv('VALOR TOTAL GERAL EM ESTOQUE:'), 0, 0, 'L');
                $pdf->Cell(65, 8, 'R$ ' . number_format($totalGeral, 2, ',', '.'), 0, 1, 'R');

                $pdf->Output('I', 'relatorio_valorizacao_' . date('Y-m-d') . '.pdf');
                exit;

            } elseif ($relatorio === 'movimentacoes') {
                $dataInicial = $_GET['data_inicial'] ?? '';
                $dataFinal = $_GET['data_final'] ?? '';
                $tipo = $_GET['tipo'] ?? 'todos';
                $produtoId = $_GET['produto_id'] ?? '';
                $categoria = $_GET['categoria'] ?? '';

                $erros = Validacao::periodoRelatorio([
                    'data_inicial' => $dataInicial,
                    'data_final' => $dataFinal
                ]);

                if ($erros !== []) {
                    echo 'Filtros de data inicial e final sao obrigatorios e devem formar um periodo valido.';
                    exit;
                }

                $dados = $this->model->buscarMovimentacoesPorPeriodo($dataInicial, $dataFinal, $tipo, $produtoId, $categoria);

                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(0, 10, $this->conv('Relatório de Movimentações por Período'), 0, 1, 'C');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 6, $this->conv('Período: ' . date('d/m/Y', strtotime($dataInicial)) . ' a ' . date('d/m/Y', strtotime($dataFinal))), 0, 1, 'C');
                $pdf->Cell(0, 6, $this->conv('Tipo: ' . ($tipo === 'entrada' ? 'Entradas' : ($tipo === 'saida' ? 'Saídas' : 'Todas'))), 0, 1, 'C');
                $pdf->Cell(0, 6, $this->conv('Gerado em: ' . date('d/m/Y H:i')), 0, 1, 'C');
                $pdf->Ln(5);

                // Table Headers
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(28, 8, $this->conv('Data/Hora'), 1, 0, 'C', true);
                $pdf->Cell(50, 8, $this->conv('Produto'), 1, 0, 'L', true);
                $pdf->Cell(20, 8, $this->conv('Tipo'), 1, 0, 'C', true);
                $pdf->Cell(18, 8, $this->conv('Qtd'), 1, 0, 'C', true);
                $pdf->Cell(38, 8, $this->conv('Motivo'), 1, 0, 'L', true);
                $pdf->Cell(36, 8, $this->conv('Responsável'), 1, 1, 'L', true);

                $pdf->SetFont('Arial', '', 8);
                $fill = false;

                $motivos = [
                    'compra' => 'Compra',
                    'devolucao' => 'Devolução',
                    'transferencia' => 'Transferência',
                    'venda' => 'Venda',
                    'consumo_interno' => 'Consumo interno',
                    'perda' => 'Perda',
                    'avaria' => 'Avaria',
                    'entrada_manual' => 'Entrada manual',
                    'saida_manual' => 'Saída manual'
                ];

                $totalEntradas = 0;
                $totalSaidas = 0;

                foreach ($dados as $row) {
                    if ($row['tipo'] === 'entrada') {
                        $totalEntradas += (int) $row['quantidade'];
                    } else {
                        $totalSaidas += (int) $row['quantidade'];
                    }

                    $motivoTraduzido = $motivos[$row['motivo']] ?? ucfirst(str_replace('_', ' ', $row['motivo']));

                    $pdf->Cell(28, 7, date('d/m/Y H:i', strtotime($row['data_hora'])), 1, 0, 'C', $fill);
                    $pdf->Cell(50, 7, $this->conv($row['produto_nome']), 1, 0, 'L', $fill);
                    $pdf->Cell(20, 7, $this->conv(ucfirst($row['tipo'])), 1, 0, 'C', $fill);
                    $pdf->Cell(18, 7, $row['quantidade'], 1, 0, 'C', $fill);
                    $pdf->Cell(38, 7, $this->conv($motivoTraduzido), 1, 0, 'L', $fill);
                    $pdf->Cell(36, 7, $this->conv($row['usuario_nome'] ?: '-'), 1, 1, 'L', $fill);
                    
                    $fill = !$fill;
                }

                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, $this->conv('RESUMO DO PERÍODO:'), 0, 1, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(50, 6, $this->conv('Total de Entradas: ' . $totalEntradas), 0, 1, 'L');
                $pdf->Cell(50, 6, $this->conv('Total de Saídas: ' . $totalSaidas), 0, 1, 'L');

                $pdf->Output('I', 'relatorio_movimentacoes_' . $dataInicial . '_a_' . $dataFinal . '.pdf');
                exit;
            }

            echo 'Relatório não especificado.';
            exit;
        }
    }
