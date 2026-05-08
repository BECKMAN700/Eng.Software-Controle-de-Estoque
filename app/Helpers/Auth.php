<?php

require_once __DIR__ . '/Sessao.php';

class Auth
{   
    public static function exigirLogin(): void  
    {   
        if (!Sessao::estaLogado()) {    

            Sessao::setFlashErro('Faça login.');

            header('Location: index.php?acao=login');
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        return Sessao::getPapel() === 'admin';
    }

   public static function exigirAdmin(): void
{
    self::exigirLogin();

    if (!self::isAdmin()) {

        Sessao::setFlashErro(
            'Você não tem permissão para acessar esta funcionalidade.'
        );

        header('Location: index.php?acao=listar');
        exit;
    }
}

    public static function isEstoquista(): bool
    {
        return Sessao::getPapel() === 'estoquista';
    }
}