<?php

class TestCase
{
    private int $executados = 0;
    private array $falhas = [];

    public function assertTrue(bool $condicao, string $mensagem): void
    {
        $this->executados++;

        if (!$condicao) {
            $this->falhas[] = $mensagem;
        }
    }

    public function assertFalse(bool $condicao, string $mensagem): void
    {
        $this->assertTrue(!$condicao, $mensagem);
    }

    public function assertEquals($esperado, $atual, string $mensagem): void
    {
        $this->executados++;

        if ($esperado !== $atual) {
            $this->falhas[] = $mensagem . ' Esperado: ' . var_export($esperado, true) . '. Atual: ' . var_export($atual, true) . '.';
        }
    }

    public function assertArrayHasKey(string $chave, array $array, string $mensagem): void
    {
        $this->executados++;

        if (!array_key_exists($chave, $array)) {
            $this->falhas[] = $mensagem;
        }
    }

    public function executados(): int
    {
        return $this->executados;
    }

    public function falhas(): array
    {
        return $this->falhas;
    }
}
