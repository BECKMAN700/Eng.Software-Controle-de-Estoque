<?php

require_once __DIR__ . '/../Helpers/Sessao.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';

class UsuarioController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    public function listar(): void
    {
        $usuarios = $this->model->listar();

        include __DIR__ . '/../Views/usuarios/listar.php';
    }

    public function mostrarCriar(array $dados = [], array $erros = []): void
    {
        include __DIR__ . '/../Views/usuarios/criar.php';
    }

    public function salvar(): void
    {
        $dados = [
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'senha' => (string) ($_POST['senha'] ?? ''),
            'papel' => trim((string) ($_POST['papel'] ?? 'estoquista')),
            'status' => trim((string) ($_POST['status'] ?? 'ativo')),
        ];

        $erros = $this->validar($dados);

        if ($erros !== []) {
            $this->mostrarCriar($dados, $erros);
            return;
        }

        if ($this->model->buscarPorEmail($dados['email'])) {
            $erros['email'] = 'Ja existe usuario cadastrado com este e-mail.';
            $this->mostrarCriar($dados, $erros);
            return;
        }

        if (!$this->model->criar($dados)) {
            $erros['geral'] = 'Nao foi possivel cadastrar o usuario.';
            $this->mostrarCriar($dados, $erros);
            return;
        }

        Sessao::setFlashSucesso('Usuario cadastrado com sucesso.');
        header('Location: index.php?acao=usuarios');
        exit;
    }

    private function validar(array $dados): array
    {
        $erros = [];

        if ($dados['nome'] === '') {
            $erros['nome'] = 'Informe o nome do usuario.';
        }

        if ($dados['email'] === '') {
            $erros['email'] = 'Informe o e-mail do usuario.';
        } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um e-mail valido.';
        }

        if (trim($dados['senha']) === '') {
            $erros['senha'] = 'Informe uma senha.';
        } elseif (strlen($dados['senha']) < 6) {
            $erros['senha'] = 'A senha deve ter pelo menos 6 caracteres.';
        }

        if (!in_array($dados['papel'], ['admin', 'estoquista'], true)) {
            $erros['papel'] = 'Selecione um papel valido.';
        }

        if (!in_array($dados['status'], ['ativo', 'inativo'], true)) {
            $erros['status'] = 'Selecione um status valido.';
        }

        return $erros;
    }
}
