<?php

/**
 * Helpers/Sessao.php
 * Ponto único de gerenciamento de sessão PHP.
 * Nenhum outro arquivo deve chamar session_start() diretamente.
 */
class Sessao
{

    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function definirUsuario(int $id, string $nome, string $email, string $papel): void
    {
        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_papel'] = $papel;
    }

    public static function estaLogado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    public static function getId(): ?int
    {
        return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
    }

    public static function getNome(): string
    {
        return (string) ($_SESSION['usuario_nome'] ?? '');
    }

    public static function getEmail(): string
    {
        return (string) ($_SESSION['usuario_email'] ?? '');
    }

    public static function getPapel(): string
    {
        return (string) ($_SESSION['usuario_papel'] ?? '');
    }

    public static function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['csrf_token'];
    }

    public static function validarCsrfToken($token): bool
    {
        $tokenSessao = (string) ($_SESSION['csrf_token'] ?? '');
        $tokenRecebido = (string) $token;

        return $tokenSessao !== '' && hash_equals($tokenSessao, $tokenRecebido);
    }

    public static function setFlashSucesso(string $mensagem): void
    {
        $_SESSION['flash_sucesso'] = $mensagem;
    }

    public static function setFlashErro(string $mensagem): void
    {
        $_SESSION['flash_erro'] = $mensagem;
    }

    public static function getFlashSucesso(): string
    {
        $msg = (string) ($_SESSION['flash_sucesso'] ?? '');
        unset($_SESSION['flash_sucesso']);
        return $msg;
    }

    public static function getFlashErro(): string
    {
        $msg = (string) ($_SESSION['flash_erro'] ?? '');
        unset($_SESSION['flash_erro']);
        return $msg;
    }

    public static function destruir(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
