<?php

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';

$controller = new ProdutoController();
$acao = $_GET['acao'] ?? 'listar';

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

    default:
        echo "Ação inválida.";
        break;
}