<?php

require_once __DIR__ . '/../Models/ProdutoModel.php';

class ProdutoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }
     public function listar()
     {
        $produtos = $this->model->listar(); 
        include __DIR__ .'/../Views/produtos/listar.php';
     }
}