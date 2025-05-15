<?php
include './../db/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data['nome'];
$valor = $data['valor'];
$estoque = $data['estoque'];

////// Insere produto
$insertProduto = $conn->prepare("INSERT INTO produtos (produto_name, valor) VALUES (?, ?)");
$insertProduto->bind_param("sd", $nome, $valor);

if ($insertProduto->execute()) {
    $produto_id = $conn->insert_id; 
    $insertEstoque = $conn->prepare("INSERT INTO estoque (produto_id, quantidade) VALUES (?, ?)");
    $insertEstoque->bind_param("ii", $produto_id, $estoque);
    $insertEstoque->execute();

    echo json_encode(["message" => "Produto cadastrado com sucesso."]);
} else {
    echo json_encode(["error" => "Erro ao cadastrar produto."]);
}

$conn->close();
?>
