<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../app/models/Pedido.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../app/helpers/RespostaJson.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(405, ['status' => 'error', 'message' => 'Método não permitido']);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['pedido_id']) || !isset($data['status'])) {
    responderJson(400, ['status' => 'error', 'message' => 'Dados incompletos']);
}

$pedido_id = $data['pedido_id'];
$status = strtolower($data['status']);

$valores_validos = ['pendente', 'pago', 'cancelado', 'enviado'];
if (!in_array($status, $valores_validos)) {
    responderJson(400, [
        'status' => 'error',
        'message' => "Status inválido: $status",
        'pedido_id' => $pedido_id
    ]);
}

try {
    $pdo = conexao();
    $pedidoModel = new PedidoModel($pdo);

    if (!$pedidoModel->pedidoExiste($pedido_id)) {
        responderJson(404, [
            'status' => 'error',
            'message' => 'Pedido não encontrado',
            'pedido_id' => $pedido_id
        ]);
    }

    if ($status === 'cancelado') {
        $resultado = $pedidoModel->removerPedido($pedido_id);
    } else {
        $resultado = $pedidoModel->atualizarStatus($pedido_id, $status);
    }

    responderJson(200, [
        'status' => 'success',
        'message' => "Status do pedido atualizado para $status",
        'pedido_id' => $pedido_id
    ]);
} catch (Exception $e) {
    responderJson(500, [
        'status' => 'error',
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}
