<?php

require_once __DIR__ . '/../app/Controllers/ProdutoController.php';

$controller = new ProdutoController();
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    default:
        echo "Ação inválida.";
        break;
}