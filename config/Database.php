<?php

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $pass;
    private $port;
    private $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->dbname = getenv('DB_NAME') ?: 'controle_estoque';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->pass = getenv('DB_PASS') ?: '';
        $this->port = getenv('DB_PORT') ?: '3306';
    }

    public function conectar()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->pass
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            error_log('Erro na conexao com o banco: ' . $e->getMessage());
            die('Erro na conexao com o banco. Verifique as configuracoes do ambiente.');
        }
    }
}
