<?php
session_start();
header('Content-Type: application/json');

///// caso precise pegar o carrinho pela a seção para teste
echo json_encode(array_values($_SESSION['carrinho'] ?? []));
