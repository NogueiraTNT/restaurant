<?php
include './../db/db.php';

header('Content-Type: application/json');

///// faz uma seleção em conjunta usando o Join para puxar dados de estoque de uma tabela separada
$sql = "SELECT 
            produtos.produto_id, 
            produtos.produto_name, 
            produtos.valor, 
            estoque.quantidade 
        FROM produtos 
        LEFT JOIN estoque ON produtos.produto_id = estoque.produto_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $produtos = array();

    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }

    echo json_encode($produtos);
} else {
    echo json_encode([]);
}

$conn->close();
?>
