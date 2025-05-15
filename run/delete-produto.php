<?php
include './../db/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["error" => "ID inválido."]);
    exit;
}

///// Exclui primeiro o estoque (se houver)
$deleteEstoque = $conn->prepare("DELETE FROM estoque WHERE produto_id = ?");
$deleteEstoque->bind_param("i", $id);
$deleteEstoque->execute();

/////// Depois exclui o produto
$deleteProduto = $conn->prepare("DELETE FROM produtos WHERE produto_id = ?");
$deleteProduto->bind_param("i", $id);
$deleteProduto->execute();

echo json_encode(["message" => "Produto excluído com sucesso."]);
$conn->close();
?>
