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

    public function cadastro(): void
    {
        if (Sessao::estaLogado()) {
            header('Location: index.php?acao=listar');
            exit;
        }

        include __DIR__ . '/../Views/auth/cadastro.php';
    }

    public function registrarCadastro(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?acao=cadastro');
            exit;
        }

        if (!Sessao::validarCsrfToken($_POST['csrf_token'] ?? '')) {
            Sessao::setFlashErro('Requisição inválida. Tente novamente.');
            header('Location: index.php?acao=cadastro');
            exit;
        }

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');
        $senhaConf = (string) ($_POST['senha_conf'] ?? '');

        if ($nome === '' || $email === '' || $senha === '' || $senhaConf === '') {
            Sessao::setFlashErro('Preencha todos os campos.');
            header('Location: index.php?acao=cadastro');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Sessao::setFlashErro('E-mail inválido.');
            header('Location: index.php?acao=cadastro');
            exit;
        }

        if ($senha !== $senhaConf) {
            Sessao::setFlashErro('A senha e a confirmação não coincidem.');
            header('Location: index.php?acao=cadastro');
            exit;
        }

        // Verifica e-mail duplicado
        $existente = $this->model->buscarPorEmail($email);
        if ($existente !== null) {
            Sessao::setFlashErro('E-mail já cadastrado. Faça login ou recupere sua senha.');
            header('Location: index.php?acao=cadastro');
            exit;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $ok = false;
        try {
            $ok = $this->model->cadastrar($nome, $email, $senhaHash);
        } catch (Exception $e) {
            $ok = false;
        }

        if ($ok) {
            Sessao::setFlashSucesso('Cadastro realizado com sucesso. Faça login.');
            header('Location: index.php?acao=login');
            exit;
        }

        Sessao::setFlashErro('Erro ao cadastrar usuário. Tente novamente mais tarde.');
        header('Location: index.php?acao=cadastro');
        exit;
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
