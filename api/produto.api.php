<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../app/controllers/api/ProdutoApiController.php';
require_once __DIR__ . '/../app/helpers/respostaJson.php';

header("Content-Type: application/json");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = strtolower(rtrim($uri, '/'));
$metodo = $_SERVER['REQUEST_METHOD'];

$controller = new ProdutoApiController();

if (strpos($uri, '/montink/api/produto') !== 0) {
    responderJson(404, ['status' => 'error', 'message' => 'Rota não encontrada']);
}

if (preg_match('#^/montink/api/produto/editar/(\d+)$#', $uri, $matches)) {
    $id = (int)$matches[1];
    if ($metodo === 'GET') {
        $controller->buscarProdutoComId($id);
    } elseif ($metodo === 'PUT') {
        $controller->atualizarProdutoComId($id);
    } else {
        responderJson(405, ['status' => 'error', 'message' => 'Método não permitido']);
    }
    exit;
}

if ($uri === '/montink/api/produto') {
    if ($metodo === 'GET') {
        $controller->index();
    } elseif ($metodo === 'POST') {
        $controller->store();
    } else {
        responderJson(405, ['status' => 'error', 'message' => 'Método não permitido']);
    }
    exit;
}

responderJson(404, ['status' => 'error', 'message' => 'Rota não encontrada']);
