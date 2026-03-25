<?php

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';

$controller = new ProdutoController();
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {

    case 'listar':
        $controller->listar();
        
    case 'criar':
        $controller->mostrarCriar();
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->salvar();
        }
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