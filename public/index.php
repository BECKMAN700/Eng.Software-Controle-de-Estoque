<?php

$acao = $_GET['acao'] ?? 'login';

if ($acao === 'login') {
    include __DIR__ . '/../app/Views/auth/login.php';
    exit;
}

if ($acao === 'autenticar') {
    $mensagem = urlencode('Autenticacao sera implementada na feature auth-sessao.');
    header('Location: index.php?acao=login&erro=' . $mensagem);
    exit;
}

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
        echo "Ação inválida.";
        break;
}
