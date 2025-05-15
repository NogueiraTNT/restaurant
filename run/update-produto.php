<?php
include './../db/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$nome = $data['nome'];
$valor = $data['valor'];
$estoque = $data['estoque'];

////// Atualiza produtos
$updateProduto = $conn->prepare("UPDATE produtos SET produto_name = ?, valor = ? WHERE produto_id = ?");
$updateProduto->bind_param("sdi", $nome, $valor, $id);
$updateProduto->execute();

////// Atualiza estoque
$checkEstoque = $conn->prepare("SELECT * FROM estoque WHERE produto_id = ?");
$checkEstoque->bind_param("i", $id);
$checkEstoque->execute();
$result = $checkEstoque->get_result();

if ($result->num_rows > 0) {
    $updateEstoque = $conn->prepare("UPDATE estoque SET quantidade = ? WHERE produto_id = ?");
    $updateEstoque->bind_param("ii", $estoque, $id);
    $updateEstoque->execute();
} else {
    $insertEstoque = $conn->prepare("INSERT INTO estoque (produto_id, quantidade) VALUES (?, ?)");
    $insertEstoque->bind_param("ii", $id, $estoque);
    $insertEstoque->execute();
}

echo json_encode(["message" => "Produto atualizado com sucesso."]);
$conn->close();
?>
