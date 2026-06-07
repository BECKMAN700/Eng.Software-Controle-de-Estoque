<?php

require_once __DIR__ . '/../Models/Dashboard.php';

class DashboardController
{
    private $model;

    public function __construct()
    {
        $this->model = new Dashboard();
    }

    public function index(): void
    {
        $resumo = $this->model->buscarResumoGeral();
        $entradasSaidas = $this->model->buscarEntradasSaidasPeriodo(30);
        $produtosCriticos = $this->model->buscarProdutosCriticos(5);
        $maisMovimentados = $this->model->buscarMaisMovimentados(5);
        $tendenciaMovimentacoes = $this->model->buscarTendenciaMovimentacoes(7);

        include __DIR__ . '/../Views/dashboard/index.php';
    }
}