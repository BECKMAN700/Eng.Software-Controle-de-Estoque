<?php

class Database
{
    private $host = '127.0.0.1';
    private $dbname = 'controle_estoque';
    private $user = 'root';
    private $pass = '';
    private $port = '3306'; // se você tiver mudado a porta, troque aqui
    private $conn;

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
            die('Erro na conexão com o banco: ' . $e->getMessage());
        }
    }
}