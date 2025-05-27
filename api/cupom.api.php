<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../app/controllers/api/CupomApiController.php';
require_once __DIR__ . '/../app/helpers/respostaJson.php';

header("Content-Type: application/json");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = strtolower(rtrim($uri, '/'));
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST' && isset($_POST['_method'])) {
    $metodo = strtoupper($_POST['_method']);
}


if (strpos($uri, '/montink/api/cupom') !== 0) {
    responderJson(404, ['status' => 'error', 'message' => 'Rota não encontrada']);
}

$controller = new CupomApiController();

if ($uri === '/montink/api/cupom/validar') {
    if ($metodo === 'POST') {
        $controller->validar();
    } else {
        responderJson(405, ['status' => 'error', 'message' => 'Método não permitido']);
    }
    exit;
}

$patternId = '#^/montink/api/cupom/(\d+)$#';
if ($uri === '/montink/api/cupom') {
    if ($metodo === 'GET') {
        $controller->listarAtivos();
    } elseif ($metodo === 'POST') {
        $controller->salvar();
    } else {
        responderJson(405, ['status' => 'error', 'message' => 'Método não permitido']);
    }
    exit;
} elseif (preg_match($patternId, $uri, $matches)) {
    $id = (int) $matches[1];
    switch ($metodo) {
        case 'GET':
            $controller->buscarPorId($id);
            break;
        case 'PUT':
        case 'PATCH':
            $controller->atualizarCupom($id);
            break;
        case 'DELETE':
            $controller->excluir($id);
            break;
        default:
            responderJson(405, ['status' => 'error', 'message' => 'Método não permitido']);
    }
    exit;
}

responderJson(404, ['status' => 'error', 'message' => 'Rota não encontrada']);
