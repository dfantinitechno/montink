<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = strtolower(rtrim($uri, '/'));

// ======================================
// ROTAS API RESTful
// ======================================

if (
    $uri === '/montink/api/produto' ||
    preg_match('#^/montink/api/produto/\d+$#', $uri) ||
    preg_match('#^/montink/api/produto/editar/\d+$#', $uri)
) {
    require_once __DIR__ . '/api/produto.api.php';
    exit;
}

if (
    $uri === '/montink/api/cupom' ||
    preg_match('#^/montink/api/cupom(/editar)?/\d+$#', $uri) ||
    $uri === '/montink/api/cupom/validar'
) {
    require_once __DIR__ . '/api/cupom.api.php';
    exit;
}

if ($uri === '/montink/api/webhook') {
    require_once __DIR__ . '/api/webhook.php';
    exit;
}

// ======================================
// ROTAS WEB 
// ======================================
switch ($uri) {
    case '/montink':
    case '/montink/index.php':
        $controller = 'HomeController';
        $action = 'index';
        break;

    case '/montink/produto/novo':
        $controller = 'ProdutoController';
        $action = 'criar';
        break;

    case '/montink/produto/editar':
        $controller = 'ProdutoController';
        $action = 'editar';
        break;

    case '/montink/cupom':
        $controller = 'CupomController';
        $action = 'index';
        break;

    case '/montink/cupom/novo':
        $controller = 'CupomController';
        $action = 'novo';
        break;

    case '/montink/cupom/editar':
        $controller = 'CupomController';
        $action = 'editar';
        break;

    case '/montink/carrinho':
        $controller = 'CarrinhoController';
        $action = 'index';
        break;

    case '/montink/carrinho/adicionar':
        $controller = 'CarrinhoController';
        $action = 'adicionar';
        break;

    case '/montink/carrinho/remover':
        $controller = 'CarrinhoController';
        $action = 'removerItem';
        break;

    case '/montink/carrinho/finalizar':
        $controller = 'CarrinhoController';
        $action = 'finalizarCompra';
        break;

    case '/montink/pedido/sucesso':
        $controller = 'PedidoController';
        $action = 'sucesso';
        break;

    case '/montink/pedidos':
        $controller = 'PedidoController';
        $action = 'index';
        break;

    default:
        http_response_code(404);
        echo "Página não encontrada";
        exit;
}

// ======================================
// CARREGAMENTO DO CONTROLLER
// ======================================

$controllerFile = __DIR__ . "/app/controllers/{$controller}.php";

if (!file_exists($controllerFile)) {
    http_response_code(500);
    echo "Controller não encontrado: {$controller}";
    exit;
}

require_once $controllerFile;

if (!class_exists($controller)) {
    http_response_code(500);
    echo "Classe '{$controller}' não foi encontrada.";
    exit;
}

$ctrl = new $controller();

if (!method_exists($ctrl, $action)) {
    http_response_code(500);
    echo "Método '{$action}' não existe no controller '{$controller}'";
    exit;
}

$ctrl->$action();
