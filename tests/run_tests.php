<?php

require_once __DIR__ . '/TestCase.php';

$sessionPath = __DIR__ . '/tmp';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

ini_set('session.save_path', $sessionPath);

require_once __DIR__ . '/AuthTest.php';
require_once __DIR__ . '/PermissaoTest.php';
require_once __DIR__ . '/ApiResponseTest.php';
require_once __DIR__ . '/ProdutoApiTest.php';
require_once __DIR__ . '/MovimentacaoApiTest.php';
require_once __DIR__ . '/InventarioTest.php';
require_once __DIR__ . '/Unit/ValidacaoTest.php';
require_once __DIR__ . '/Feature/ApiProdutosTest.php';

$teste = new TestCase();

testeAuth($teste);
testePermissao($teste);
testeApiResponse($teste);
testeProdutoApi($teste);
testeMovimentacaoApi($teste);
testeInventario($teste);
testeUnitValidacao($teste);
testeFeatureApiProdutos($teste);

if ($teste->falhas() !== []) {
    echo "Falhas encontradas:\n";

    foreach ($teste->falhas() as $falha) {
        echo "- {$falha}\n";
    }

    exit(1);
}

echo "Todos os testes passaram. Total de assercoes: " . $teste->executados() . "\n";
