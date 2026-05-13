<?php

class Validacao
{
    public static function camposObrigatorios(array $dados, array $campos): array
    {
        $erros = [];

        foreach ($campos as $campo => $mensagem) {
            $valor = $dados[$campo] ?? null;

            if ($valor === null || trim((string) $valor) === '') {
                $erros[$campo] = $mensagem;
            }
        }

        return $erros;
    }

    public static function produto(array $dados): array
    {
        $erros = self::camposObrigatorios($dados, [
            'nome' => 'O nome do produto e obrigatorio.',
        ]);

        $quantidade = (int) ($dados['quantidade'] ?? 0);
        $estoqueMinimo = (int) ($dados['estoque_minimo'] ?? 0);
        $estoqueMaximo = $dados['estoque_maximo'] ?? null;
        $preco = (float) ($dados['preco'] ?? 0);

        if ($quantidade < 0) {
            $erros['quantidade'] = 'A quantidade nao pode ser negativa.';
        }

        if ($estoqueMinimo < 0) {
            $erros['estoque_minimo'] = 'O estoque minimo nao pode ser negativo.';
        }

        if ($estoqueMaximo !== null && $estoqueMaximo !== '' && (int) $estoqueMaximo < $estoqueMinimo) {
            $erros['estoque_maximo'] = 'O estoque maximo deve ser maior ou igual ao estoque minimo.';
        }

        if ($preco < 0) {
            $erros['preco'] = 'O preco nao pode ser negativo.';
        }

        $statusValidos = ['ativo', 'inativo', 'descontinuado'];
        $status = trim((string) ($dados['status'] ?? 'ativo'));

        if (!in_array($status, $statusValidos, true)) {
            $erros['status'] = 'Status invalido.';
        }

        return $erros;
    }

    public static function movimentacao(array $dados): array
    {
        $erros = self::camposObrigatorios($dados, [
            'produto_id' => 'Informe o produto_id.',
            'tipo' => 'Informe o tipo da movimentacao.',
            'motivo' => 'Informe o motivo da movimentacao.',
            'quantidade' => 'Informe a quantidade.',
        ]);

        $tipo = trim((string) ($dados['tipo'] ?? ''));
        $produtoId = (int) ($dados['produto_id'] ?? 0);
        $quantidade = (int) ($dados['quantidade'] ?? 0);

        if ($produtoId <= 0) {
            $erros['produto_id'] = 'Informe um produto_id valido.';
        }

        if ($tipo !== '' && !in_array($tipo, ['entrada', 'saida'], true)) {
            $erros['tipo'] = 'Tipo de movimentacao invalido.';
        }

        if ($quantidade <= 0) {
            $erros['quantidade'] = 'A quantidade deve ser maior que zero.';
        }

        $motivo = trim((string) ($dados['motivo'] ?? ''));
        $motivosEntrada = ['compra', 'devolucao', 'transferencia'];
        $motivosSaida = ['venda', 'consumo_interno', 'perda', 'avaria'];

        if ($tipo === 'entrada' && $motivo !== '' && !in_array($motivo, $motivosEntrada, true)) {
            $erros['motivo'] = 'Motivo de entrada invalido.';
        }

        if ($tipo === 'saida' && $motivo !== '' && !in_array($motivo, $motivosSaida, true)) {
            $erros['motivo'] = 'Motivo de saida invalido.';
        }

        return $erros;
    }
}
