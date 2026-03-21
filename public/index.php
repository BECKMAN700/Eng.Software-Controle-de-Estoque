<?php

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';

$controller = new ProdutoController();
$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case 'criar':
        $controller->mostrarCriar();
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->salvar();
        }
        break;

    default:
        echo "Ação inválida.";
        break;
}