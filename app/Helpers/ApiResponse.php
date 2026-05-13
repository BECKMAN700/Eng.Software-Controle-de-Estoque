<?php

class ApiResponse
{
    public static function montar(bool $erro, string $mensagem, $dados = null, array $extras = []): array
    {
        $resposta = [
            'erro' => $erro,
            'mensagem' => $mensagem,
            'dados' => $dados,
        ];

        foreach ($extras as $chave => $valor) {
            $resposta[$chave] = $valor;
        }

        return $resposta;
    }

    public static function enviar(bool $erro, string $mensagem, $dados = null, int $status = 200, array $extras = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(self::montar($erro, $mensagem, $dados, $extras), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function sucesso(string $mensagem, $dados = null, int $status = 200): void
    {
        self::enviar(false, $mensagem, $dados, $status);
    }

    public static function erro(string $mensagem, int $status = 400, array $erros = []): void
    {
        $extras = $erros === [] ? [] : ['erros' => $erros];
        self::enviar(true, $mensagem, null, $status, $extras);
    }
}
