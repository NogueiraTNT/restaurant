<?php
session_start();
header('Content-Type: application/json');

include './../db/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['produto_id'];
$qtd = intval($data['quantidade']);

////// Verifica se produto existe e tem estoque
$stmt = $conn->prepare("SELECT produto_name, valor, quantidade FROM produtos 
                        JOIN estoque ON produtos.produto_id = estoque.produto_id
                        WHERE produtos.produto_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

if (!$produto || $produto['quantidade'] < $qtd) {
    echo json_encode(['error' => 'Produto não encontrado ou estoque insuficiente']);
    exit;
}

////// Adiciona ao carrinho
$_SESSION['carrinho'][$id] = [
    'id' => $id,
    'nome' => $produto['produto_name'],
    'valor' => $produto['valor'],
    'quantidade' => $qtd
];

echo json_encode(['message' => 'Produto adicionado ao carrinho com sucesso']);
