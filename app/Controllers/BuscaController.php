<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Models/ProdutoModel.php';
require_once __DIR__ . '/../Models/InventarioModel.php';

/**
 * Busca global do sistema.
 *
 * Consulta produtos, inventários e movimentações em um único ponto e
 * devolve resultados agrupados em JSON, consumidos pela paleta de comandos
 * (Ctrl+K / "/") da topbar.
 */
class BuscaController
{
    public function global(): void
    {
        Auth::exigirLogin();

        header('Content-Type: application/json; charset=utf-8');

        $termo = trim((string) ($_GET['q'] ?? ''));
        $grupos = [];

        if (mb_strlen($termo) < 2) {
            echo json_encode(['grupos' => $grupos], JSON_UNESCAPED_UNICODE);
            return;
        }

        $termoLower = mb_strtolower($termo);

        // ── Produtos ────────────────────────────────────────────────
        $produtoModel = new ProdutoModel();
        $produtos = $produtoModel->listarFiltrados($termo);
        $itensProdutos = [];
        foreach (array_slice($produtos, 0, 6) as $p) {
            $itensProdutos[] = [
                'titulo'    => $p['nome'],
                'subtitulo' => ($p['codigo'] ?? '') !== '' ? 'Cód. ' . $p['codigo'] : ($p['categoria'] ?? 'Produto'),
                'url'       => 'index.php?acao=historico_movimentacoes&id=' . (int) $p['id'],
            ];
        }
        if ($itensProdutos !== []) {
            $grupos[] = ['rotulo' => 'Produtos', 'itens' => $itensProdutos];
        }

        // ── Inventários ─────────────────────────────────────────────
        $inventarioModel = new InventarioModel();
        $statusLabels = [
            'aberto' => 'Aberto',
            'em_conferencia' => 'Em conferência',
            'aprovado' => 'Aprovado',
            'cancelado' => 'Cancelado',
        ];
        $itensInventarios = [];
        foreach ($inventarioModel->listar() as $inv) {
            $alvo = mb_strtolower(($inv['titulo'] ?? '') . ' ' . ($inv['categoria'] ?? '') . ' #' . ($inv['id'] ?? ''));
            if (mb_strpos($alvo, $termoLower) === false) {
                continue;
            }
            $itensInventarios[] = [
                'titulo'    => '#' . $inv['id'] . ' · ' . ($inv['titulo'] ?? 'Inventário'),
                'subtitulo' => $statusLabels[$inv['status'] ?? ''] ?? ($inv['categoria'] ?? 'Inventário'),
                'url'       => 'index.php?acao=inventario_detalhar&id=' . (int) $inv['id'],
            ];
            if (count($itensInventarios) >= 5) {
                break;
            }
        }
        if ($itensInventarios !== []) {
            $grupos[] = ['rotulo' => 'Inventários', 'itens' => $itensInventarios];
        }

        // ── Movimentações (por produto) ─────────────────────────────
        $movimentacoes = $produtoModel->listarMovimentacoes(null, 200);
        $itensMov = [];
        foreach ($movimentacoes as $m) {
            $alvo = mb_strtolower(($m['produto_nome'] ?? '') . ' ' . ($m['produto_codigo'] ?? ''));
            if (mb_strpos($alvo, $termoLower) === false) {
                continue;
            }
            $tipo = ($m['tipo'] ?? '') === 'entrada' ? 'Entrada' : 'Saída';
            $data = !empty($m['data_hora']) ? date('d/m/Y H:i', strtotime($m['data_hora'])) : '';
            $itensMov[] = [
                'titulo'    => $m['produto_nome'] . ' · ' . $tipo . ' de ' . (int) $m['quantidade'],
                'subtitulo' => trim($data),
                'url'       => 'index.php?acao=historico_movimentacoes&id=' . (int) $m['produto_id'],
            ];
            if (count($itensMov) >= 5) {
                break;
            }
        }
        if ($itensMov !== []) {
            $grupos[] = ['rotulo' => 'Movimentações', 'itens' => $itensMov];
        }

        echo json_encode(['grupos' => $grupos], JSON_UNESCAPED_UNICODE);
    }
}
