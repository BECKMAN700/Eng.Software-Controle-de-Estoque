<?php

require_once __DIR__ . '/../Helpers/Sessao.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';

class AuthController
{
    private UsuarioModel $model;

    public function __construct()
    {
        Sessao::iniciar();
        $this->model = new UsuarioModel();
    }


    public function login(): void
    {
        if (Sessao::estaLogado()) {
            header('Location: index.php?acao=listar');
            exit;
        }

        include __DIR__ . '/../Views/auth/login.php';
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?acao=login');
            exit;
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        // Validação básica de campos
        if ($email === '' || $senha === '') {
            Sessao::setFlashErro('Preencha o e-mail e a senha.');
            header('Location: index.php?acao=login');
            exit;
        }

        // Busca o usuário pelo e-mail
        $usuario = $this->model->buscarPorEmail($email);

        // Verifica existência, status ativo e senha
        if (
            $usuario === null
            || ($usuario['status'] ?? '') !== 'ativo'
            || !password_verify($senha, $usuario['senha'])
        ) {
            Sessao::setFlashErro('E-mail ou senha inválidos.');
            header('Location: index.php?acao=login');
            exit;
        }

        session_regenerate_id(true);

        Sessao::definirUsuario(
            (int) $usuario['id'],
            (string) $usuario['nome'],
            (string) $usuario['email'],
            (string) $usuario['papel']
        );

        header('Location: index.php?acao=listar');
        exit;
    }

    public function logout(): void
    {
        Sessao::destruir();
        header('Location: index.php?acao=login');
        exit;
    }
}
