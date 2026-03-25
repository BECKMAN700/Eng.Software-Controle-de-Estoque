<!DOCTYPE html> 
<html lang="pt-BR"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Movimentar Estoque</title> 
</head> 
<body> 
    <h1>Movimentar Estoque</h1> 
 
    <p><strong>Produto:</strong> <?= 
htmlspecialchars($produto['nome']) ?></p> 
    <p><strong>Quantidade atual:</strong> <?= $produto['quantidade'] 
?></p> 
 
    <form action="index.php?acao=movimentar" method="POST"> 
        <input type="hidden" name="id" value="<?= $produto['id'] 
?>"> 
 
        <p> 
            <label>Tipo de movimentação:</label><br> 
            <select name="tipo" required> 
                <option value="entrada">Entrada</option> 
                <option value="saida">Saída</option> 
            </select> 
        </p> 
 
        <p> 
            <label>Quantidade:</label><br> 
            <input type="number" name="quantidade" min="1" required> 
        </p> 
 
        <button type="submit">Confirmar</button> 
<a href="index.php?acao=listar">Voltar</a> 
</form> 
</body> 
</html> 