<?php

/**
 * public/index.php — Front Controller
 *
 * Ponto único de entrada. Toda sessão é gerenciada pelo helper Sessao,
 * que é carregado aqui para garantir session_start() em um único lugar.
 */

require_once __DIR__ . '/../app/Helpers/Sessao.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

Sessao::iniciar();

$acao = trim($_GET['acao'] ?? 'login');

// ─── Rotas de autenticação (públicas) ────────────────────────────────────────

$authController = new AuthController();

if ($acao === 'login') {
    $authController->login();
    exit;
}

if ($acao === 'autenticar') {
    $authController->autenticar();
    exit;
}

if ($acao === 'logout') {
    $authController->logout();
    exit;
}

// ─── Proteção de sessão: redireciona para login se não estiver autenticado ───

if (!Sessao::estaLogado()) {
    header('Location: index.php?acao=login');
    exit;
}

// ─── Rotas protegidas ────────────────────────────────────────────────────────

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';

$controller = new ProdutoController();

switch ($acao) {
    case 'listar':
        $controller->listar();
        break;

    case 'criar':
        $controller->mostrarCriar();
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->salvar();
        }
        break;

    case 'editar':
        $controller->mostrarEditar();
        break;

    case 'atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->atualizar();
        }
        break;

    case 'excluir':
        $controller->excluir();
        break;

    case 'movimentar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->movimentar();
        } else {
            $controller->mostrarMovimentar();
        }
        break;

    case 'saida':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->registrarSaida();
        } else {
            $controller->mostrarSaida();
        }
        break;

    case 'detalhes_saida':
        $controller->mostrarDetalhesSaida();
        break;

    case 'historico_movimentacoes':
        $controller->mostrarHistoricoMovimentacoes();
        break;

    case 'entrada':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->registrarEntrada();
        } else {
            $controller->mostrarEntrada();
        }
        break;

    case 'catalogo':
        $controller->catalogo();
        break;

    case 'relatorios':
        $controller->relatorios();
        break;

    default:
        echo 'Ação inválida.';
        break;
}
