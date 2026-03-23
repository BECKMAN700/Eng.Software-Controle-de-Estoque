<?php

require_once __DIR__ . '/../Models/ProdutoModel.php';

class ProdutoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    public function mostrarCriar()
    {
        include __DIR__ . '/../Views/produtos/criar.php';
    }

    public function salvar()
    {
        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'codigo' => $_POST['codigo'] ?? '',
            'quantidade' => $_POST['quantidade'] ?? 0,
            'preco' => $_POST['preco'] ?? 0
        ];

        $this->model->criar($dados);
        header('Location: index.php?acao=listar');
        exit;
    }

    

}