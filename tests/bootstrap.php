<?php

/**
 * Bootstrap dos testes (PHPUnit).
 *
 * Configura o diretorio de sessao usado pelos testes que exercitam
 * autenticacao, sessao e permissoes, mantendo o comportamento isolado
 * e independente do servidor web.
 */

$sessionPath = __DIR__ . '/tmp';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

ini_set('session.save_path', $sessionPath);

// Em ambiente de teste (CLI) nao usamos cookies de sessao:
// evita avisos de "headers already sent" ao iniciar a sessao.
ini_set('session.use_cookies', '0');
ini_set('session.use_only_cookies', '0');
ini_set('session.cache_limiter', '');
session_cache_limiter('');
