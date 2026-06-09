<?php

/**
 * public/index.php — Front Controller
 *
 * Ponto único de entrada. Toda sessão é gerenciada pelo helper Sessao,
 * que é carregado aqui para garantir session_start() em um único lugar.
 */

require_once __DIR__ . '/../app/Helpers/Auth.php';
require_once __DIR__ . '/../app/Helpers/Sessao.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

Sessao::iniciar();

$acao = trim($_GET['acao'] ?? 'login');

// Interceptar caminhos sugeridos para exportação de relatórios
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
if (strpos($pathInfo, '/relatorios/exportar-pdf') !== false || strpos($requestUri, '/relatorios/exportar-pdf') !== false) {
    $acao = 'exportar-pdf';
} elseif (strpos($pathInfo, '/relatorios/exportar-csv') !== false || strpos($requestUri, '/relatorios/exportar-csv') !== false) {
    $acao = 'exportar-csv';
}

$acoesApi = ['api_produtos', 'api_movimentacoes'];
$ehApi = in_array($acao, $acoesApi, true);

function exigirPostComCsrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Sessao::setFlashErro('Requisicao invalida.');
        header('Location: index.php?acao=listar');
        exit;
    }

    if (!Sessao::validarCsrfToken($_POST['csrf_token'] ?? '')) {
        Sessao::setFlashErro('Sessao expirada. Tente novamente.');
        header('Location: index.php?acao=listar');
        exit;
    }
}

// ─── Rotas de autenticação (públicas) ────────────────────────────────────────

$authController = new AuthController();

if ($acao === 'login') {
    $authController->login();
    exit;
}

if ($acao === 'autenticar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Sessao::validarCsrfToken($_POST['csrf_token'] ?? '')) {
        Sessao::setFlashErro('Sessao expirada. Tente novamente.');
        header('Location: index.php?acao=login');
        exit;
    }

    $authController->autenticar();
    exit;
}

if ($acao === 'logout') {
    exigirPostComCsrf();
    $authController->logout();
    exit;
}

// ─── Proteção de sessão: redireciona para login se não estiver autenticado ───

if (!$ehApi) {
    Auth::exigirLogin();
}


// ─── Rotas protegidas ────────────────────────────────────────────────────────

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';
require_once __DIR__ . '/../app/Controllers/UsuarioController.php';
require_once __DIR__ . '/../app/Controllers/InventarioController.php';
require_once __DIR__ . '/../app/Controllers/RelatorioController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';

$controller = new ProdutoController();
$usuarioController = new UsuarioController();
$inventarioController = new InventarioController();
$relatorioController = new RelatorioController();
$dashboardController = new DashboardController();   

switch ($acao) {
    // ====================== ROTAS DE INVENTÁRIO ======================
    case 'inventarios':
        $inventarioController->inventarios();
        break;

    case 'inventario_criar':
        $inventarioController->inventario_criar();
        break;

    case 'inventario_salvar':
        exigirPostComCsrf();
        $inventarioController->inventario_salvar();
        break;

    case 'inventario_detalhar':
        $inventarioController->inventario_detalhar();
        break;

    case 'inventario_contagem':
        $inventarioController->inventario_contagem();
        break;

    case 'inventario_salvar_contagem':
        exigirPostComCsrf();
        $inventarioController->inventario_salvar_contagem();
        break;

    case 'inventario_divergencias':
        $inventarioController->inventario_divergencias();
        break;

    case 'inventario_auditoria':
        Auth::exigirAdmin();
        $inventarioController->inventario_auditoria();
        break;

    case 'inventario_aprovar':
        exigirPostComCsrf();
        $inventarioController->inventario_aprovar();
        break;

    // ====================== OUTRAS ROTAS ======================
    case 'dashboard':
        $dashboardController->index();
        break;

    case 'listar':
        $controller->listar();
        break;

    case 'criar':
        Auth::exigirAdmin();
        $controller->mostrarCriar();
        break;

    case 'salvar':
        Auth::exigirAdmin();
        exigirPostComCsrf();
        $controller->salvar();
        break;

    case 'editar':
        Auth::exigirAdmin();
        $controller->mostrarEditar();
        break;

    case 'atualizar':
        Auth::exigirAdmin();
        exigirPostComCsrf();
        $controller->atualizar();
        break;

    case 'excluir':
        Auth::exigirAdmin();
        exigirPostComCsrf();
        $controller->excluir();
        break;

    case 'movimentar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            exigirPostComCsrf();
            $controller->movimentar();
        } else {
            $controller->mostrarMovimentar();
        }
        break;

    case 'saida':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            exigirPostComCsrf();
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
            exigirPostComCsrf();
            $controller->registrarEntrada();
        } else {
            $controller->mostrarEntrada();
        }
        break;

    case 'catalogo':
        $controller->catalogo();
        break;

    case 'relatorios':
        $relatorioController->index();
        break;

    case 'giro_estoque':
        $relatorioController->giroEstoque();
        break;

    case 'valorizacao':
        $relatorioController->valorizacao();
        break;

    case 'movimentacoes_periodo':
        $relatorioController->movimentacoesPeriodo();
        break;

    case 'exportar-pdf':
        $relatorioController->exportarPdf();
        break;

    case 'exportar-csv':
        $relatorioController->exportarCsv();
        break;

    case 'divergencias':
        $controller->mostrarDivergencias();
        break;

    case 'usuarios':
        Auth::exigirAdmin();
        $usuarioController->listar();
        break;

    case 'usuario_criar':
        Auth::exigirAdmin();
        $usuarioController->mostrarCriar();
        break;

    case 'usuario_salvar':
        Auth::exigirAdmin();
        exigirPostComCsrf();
        $usuarioController->salvar();
        break;

    case 'api_produtos':
        $controller->apiProdutos();
        break;

    case 'api_movimentacoes':
        $controller->apiMovimentacoes();
        break;

    default:
        echo 'Ação inválida.';
        break;
}
