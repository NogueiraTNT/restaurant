<?php
include './../db/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$status = 'finalizado';

//// Atualiza produtos
$updateProduto = $conn->prepare("UPDATE pedidos SET status = ? WHERE pedido_id = ?");
$updateProduto->bind_param("si", $status, $id);
$updateProduto->execute();

echo json_encode(["message" => "Produto atualizado com sucesso."]);
$conn->close();
?>
