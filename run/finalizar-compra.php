<?php
session_start();
header('Content-Type: application/json');
include './../db/db.php';

$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    echo json_encode(['error' => 'Carrinho vazio']);
    exit;
}

$valor_total = 0;
$frete = 0;
$desconto = 0;
$pedido_array = [];

$data = json_decode(file_get_contents("php://input"), true);
$cep = $data['cep'] ?? null;
$cupon = $data['cupon'] ?? null;
///// Mais um teste se tem o CEP
if (!$cep) {
    echo json_encode(['error' => 'CEP não informado.']);
    exit;
}

/// Verifica se existe cupom e aplica desconto
if ($cupon) {
    $stmtc = $conn->prepare("SELECT valor, quantidade FROM cupons WHERE cupon_name = ?");
    $stmtc->bind_param("s", $cupon);
    $stmtc->execute();
    $res = $stmtc->get_result();
    $cuponData = $res->fetch_assoc();

    if ($cuponData) {
        if ($cuponData['quantidade'] > 0) {
            $desconto = (float)$cuponData['valor'];

            ///// Atualiza a quantidade do cupom (-1)
            $stmtcUpdate = $conn->prepare("UPDATE cupons SET quantidade = quantidade - 1 WHERE cupon_name = ?");
            $stmtcUpdate->bind_param("s", $cupon);
            $stmtcUpdate->execute();
        } else {
            echo json_encode(['error' => 'Cupom esgotado.']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Cupom inválido.']);
        exit;
    }
}

///// Calcula valor total dos produtos
foreach ($carrinho as $item) {
    $id = $item['id'];
    $qtd = $item['quantidade'];

    /////// Buscar nome e valor do produto
    $stmt = $conn->prepare("SELECT quantidade, produto_name, valor FROM estoque JOIN produtos ON estoque.produto_id = produtos.produto_id WHERE estoque.produto_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $produto = $res->fetch_assoc();

    if (!$produto || $produto['quantidade'] < $qtd) {
        echo json_encode(['error' => "Estoque insuficiente para produto '{$produto['produto_name']}'"]);
        exit;
    }

    $valor_total += $produto['valor'] * $qtd;

    $pedido_array[] = [$produto['produto_name'], $qtd];
}

////// Aplica desconto
$valor_total -= $desconto;
if ($valor_total < 0) $valor_total = 0;

//// Calcular frete
if ($valor_total >= 52 && $valor_total <= 166.59) {
    $frete = 15.00;
} elseif ($valor_total > 200) {
    $frete = 0.00;
} else {
    $frete = 20.00;
}

$pedido_json = json_encode($pedido_array, JSON_UNESCAPED_UNICODE);
$stmt = $conn->prepare("INSERT INTO pedidos (pedido, valor_total, frete, desconto) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sddd", $pedido_json, $valor_total, $frete, $desconto);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Erro ao salvar o pedido no banco de dados']);
    exit;
}

////// Atualizar o estoque
foreach ($carrinho as $item) {
    $id = $item['id'];
    $qtd = $item['quantidade'];

    $update = $conn->prepare("UPDATE estoque SET quantidade = quantidade - ? WHERE produto_id = ?");
    $update->bind_param("ii", $qtd, $id);
    $update->execute();
}

/////// Limpar carrinho da seção
unset($_SESSION['carrinho']);

echo json_encode([
    'message' => 'Compra finalizada e registrada com sucesso',
    'valor_total' => $valor_total,
    'frete' => $frete,
    'desconto_aplicado' => $desconto
]);
