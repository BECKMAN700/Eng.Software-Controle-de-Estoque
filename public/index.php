<?php
// Arquivo: public/index.php

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';
require_once __DIR__ . '/../app/Models/ProdutoModel.php';

$model = new ProdutoModel();
$controller = new ProdutoController();

$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {

    case 'listar':
        $controller->listar();
        break;

    default:
        echo "Ação inválida.";
        break;
} 