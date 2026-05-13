<?php

require_once __DIR__ . '/../../config/Database.php';

class UsuarioModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    private function normalizarPapel($papel): string
    {
        $papel = trim((string) $papel);
        $papeisValidos = ['admin', 'estoquista'];

        return in_array($papel, $papeisValidos, true) ? $papel : 'estoquista';
    }

    private function normalizarStatus($status): string
    {
        $status = trim((string) $status);
        $statusValidos = ['ativo', 'inativo'];

        return in_array($status, $statusValidos, true) ? $status : 'ativo';
    }

    private function prepararSenha($senha): string
    {
        $senha = (string) $senha;
        $infoSenha = password_get_info($senha);

        if (($infoSenha['algoName'] ?? 'unknown') !== 'unknown') {
            return $senha;
        }

        return password_hash($senha, PASSWORD_DEFAULT);
    }

    public function listar(): array
    {
        $sql = "SELECT id, nome, email, papel, status, criado_em, atualizado_em
                FROM usuarios
                ORDER BY nome ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorEmail($email)
    {
        $email = trim((string) $email);

        if ($email === '') {
            return null;
        }

        $sql = "SELECT id, nome, email, senha, papel, status, criado_em, atualizado_em
                FROM usuarios
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT id, nome, email, papel, status, criado_em, atualizado_em
                FROM usuarios
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function criar($dados)
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = trim((string) ($dados['email'] ?? ''));
        $senha = (string) ($dados['senha'] ?? '');
        $papel = $this->normalizarPapel($dados['papel'] ?? 'estoquista');
        $status = $this->normalizarStatus($dados['status'] ?? 'ativo');

        if ($nome === '' || $email === '' || $senha === '') {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            $sql = "INSERT INTO usuarios
                    (nome, email, senha, papel, status)
                    VALUES
                    (:nome, :email, :senha, :papel, :status)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $this->prepararSenha($senha),
                ':papel' => $papel,
                ':status' => $status
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
