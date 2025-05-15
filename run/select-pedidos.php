<?php
include './../db/db.php';

header('Content-Type: application/json');
//// Seleciona pedidos que estão em aberto e organiza
$sql = "SELECT * FROM pedidos WHERE status = 'aberto' ORDER BY pedido_id DESC";
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
