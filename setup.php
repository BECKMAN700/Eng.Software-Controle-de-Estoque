<?php

/**
 * Script de Setup - Importa o schema.sql no banco de dados
 * Acesse: http://localhost/Eng.Software-Controle-de-Estoque/setup.php
 */

require_once __DIR__ . '/config/Database.php';

try {
    $database = new Database();
    $conn = $database->conectar();

    // Ler o arquivo schema.sql
    $schemaPath = __DIR__ . '/database/schema.sql';
    
    if (!file_exists($schemaPath)) {
        throw new Exception("Arquivo schema.sql não encontrado em: $schemaPath");
    }

    $sql = file_get_contents($schemaPath);

    // Substituir CREATE TABLE por CREATE TABLE IF NOT EXISTS
    $sql = str_replace('CREATE TABLE ', 'CREATE TABLE IF NOT EXISTS ', $sql);

    // Dividir e executar cada comando SQL
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
            } catch (PDOException $e) {
                // Ignorar erros de "já existe" pois usamos IF NOT EXISTS
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }

    echo '<div style="padding: 20px; font-family: Arial; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px;">';
    echo '<h2>✓ Banco de dados configurado com sucesso!</h2>';
    echo '<p>As tabelas foram criadas/verificadas e os dados de teste foram inseridos.</p>';
    echo '<h3>Usuários de teste:</h3>';
    echo '<ul>';
    echo '<li><strong>admin@controleestoque.local</strong> - Senha: admin123 (Papel: admin)</li>';
    echo '</ul>';
    echo '<p><a href="public/index.php" style="color: #004085; text-decoration: underline;">Ir para a aplicação</a></p>';
    echo '</div>';

} catch (Exception $e) {
    echo '<div style="padding: 20px; font-family: Arial; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;">';
    echo '<h2>✗ Erro ao configurar banco de dados</h2>';
    echo '<p><strong>Erro:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    exit(1);
}
?>
