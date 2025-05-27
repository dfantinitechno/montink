<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Helpers\EmailHelper;

require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Variacao.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../models/Cupom.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';
require_once __DIR__ . '/../helpers/CarrinhoHelper.php';
require_once __DIR__ . '/../../config/conexao.php';

class CarrinhoController
{
    private $pdo;
    private $produtoModel;
    private $variacaoModel;
    private $estoqueModel;
    private $cupomModel;

    public function __construct()
    {
        $this->pdo = conexao();

        $this->produtoModel = new Produto($this->pdo);
        $this->variacaoModel = new Variacao($this->pdo);
        $this->estoqueModel = new Estoque($this->pdo);
        $this->cupomModel = new Cupom($this->pdo);
    }
    public function index()
    {
        $_SESSION['carrinho'] = $_SESSION['carrinho'] ?? [];

        $produtos = [];
        $total = 0;

        foreach ($_SESSION['carrinho'] as $item) {
            $variacoes = $this->produtoModel->buscarComVariacoesEEstoque($item['produto_id']);

            if (!$variacoes) {
                error_log("Produto ID {$item['produto_id']} não tem variações");
                continue;
            }

            $variacaoEncontrada = null;
            foreach ($variacoes as $variacao) {
                if ($variacao['variacao_id'] == $item['variacao_id']) {
                    $variacaoEncontrada = $variacao;
                    break;
                }
            }

            if (!$variacaoEncontrada) {
                error_log("Variação ID {$item['variacao_id']} não encontrada para produto ID {$item['produto_id']}");
                continue;
            }

            $produto = $this->produtoModel->buscar($item['produto_id']);
            $nomeProduto = $produto['nome'] ?? 'Produto Desconhecido';

            $subtotal = $variacaoEncontrada['preco'] * $item['quantidade'];
            $total += $subtotal;

            $produtos[] = [
                'produto_id' => $item['produto_id'],
                'variacao_id' => $item['variacao_id'],
                'nome' => $nomeProduto,
                'variacao' => $variacaoEncontrada['descricao'],
                'quantidade' => $item['quantidade'],
                'preco' => $variacaoEncontrada['preco'],
                'subtotal' => $subtotal
            ];
        }

        $frete = $this->calcularFrete($total);

        $totalGeral = $total + $frete;

        include __DIR__ . '/../views/carrinho/index.php';
    }


    public function adicionar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['produto_id']) || empty($data['quantidade'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
            return;
        }

        $produtoId = $data['produto_id'];
        $quantidade = (int)$data['quantidade'];
        $variacaoId = isset($data['variacao_id']) ? $data['variacao_id'] : null;

        $variacoes = $this->variacaoModel->listarPorProduto($produtoId);
        if (!$variacoes) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado']);
            return;
        }

        foreach ($variacoes as &$v) {
            $estoque = $this->estoqueModel->buscarPorVariacao($v['id']);
            $v['quantidade'] = $estoque ? (int)$estoque['quantidade'] : 0;
        }
        unset($v);

        $variacaoValida = null;
        foreach ($variacoes as $variacao) {
            if ($variacao['id'] == $variacaoId && $variacao['quantidade'] >= $quantidade) {
                $variacaoValida = $variacao;
                break;
            }
        }

        if (!$variacaoValida) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Quantidade excede o estoque dessa variação']);
            return;
        }

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        $itemIndex = null;
        foreach ($_SESSION['carrinho'] as $index => $item) {
            if ($item['produto_id'] == $produtoId && $item['variacao_id'] == $variacaoId) {
                $itemIndex = $index;
                break;
            }
        }

        if ($itemIndex !== null) {
            $_SESSION['carrinho'][$itemIndex]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][] = [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
                'quantidade' => $quantidade,
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Adicionado ao carrinho',
            'carrinho' => $_SESSION['carrinho']
        ]);
    }

    public function finalizarCompra()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $cep = isset($data['cep']) ? trim($data['cep']) : '';
        $enderecoCompleto = isset($data['endereco_completo']) ? trim($data['endereco_completo']) : '';
        $subtotal = isset($data['subtotal']) ? (float)$data['subtotal'] : 0;

        if (!$cep || !$enderecoCompleto || $subtotal <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
            return;
        }

        if (empty($_SESSION['carrinho'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Carrinho vazio']);
            return;
        }

        $cupomSessao = $_SESSION['cupom'] ?? null;
        $cupomId = $cupomSessao['id'] ?? null;

        try {
            $this->pdo->beginTransaction();

            $frete = $this->calcularFrete($subtotal);

            $desconto = 0;
            if ($cupomSessao) {
                $desconto = $this->cupomModel->calcularDesconto($cupomSessao, $subtotal);
            }

            $totalGeral = ($subtotal - $desconto) + $frete;

            $stmtPedido = $this->pdo->prepare("
            INSERT INTO pedidos 
                (cep, endereco_completo, subtotal, frete, desconto, total, status, cupom_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

            $stmtPedido->execute([
                $cep,
                $enderecoCompleto,
                $subtotal,
                $frete,
                $desconto,
                $totalGeral,
                'pendente',
                $cupomId,
            ]);

            $pedidoId = $this->pdo->lastInsertId();

            $pedido = [
                'pedido_id' => $pedidoId,
                'cep' => $cep,
                'endereco_completo' => $enderecoCompleto,
                'subtotal' => $subtotal,
                'frete' => $frete,
                'desconto' => $desconto,
                'total' => $totalGeral,
            ];

            $stmtItem = $this->pdo->prepare("
            INSERT INTO pedido_produtos(pedido_id, variacao_id, quantidade, preco_unitario)
            VALUES (?, ?, ?, ?)
        ");

            $produtos = [];

            foreach ($_SESSION['carrinho'] as $item) {
                $precoUnitario = 0;
                $variacoes = $this->produtoModel->buscarComVariacoesEEstoque($item['produto_id']);
                if (!$variacoes) continue;

                foreach ($variacoes as $variacao) {
                    if ($variacao['id'] == $item['variacao_id']) {
                        $produto = $this->produtoModel->buscar($item['produto_id']);
                        $produtos[] = [
                            'nome' => $produto['nome'] ?? '',
                            'variacao' => $variacao['descricao'],
                            'quantidade' => $item['quantidade'],
                            'preco' => $variacao['preco'],
                            'subtotal' => $variacao['preco'] * $item['quantidade'],
                        ];
                        $precoUnitario = $variacao['preco'];
                        break;
                    }
                }

                $stmtItem->execute([
                    $pedidoId,
                    $item['variacao_id'],
                    $item['quantidade'],
                    $precoUnitario,
                ]);

                $this->estoqueModel->diminuir($item['variacao_id'], $item['quantidade']);
            }

            $this->pdo->commit();

            if (isset($_SESSION['email_cliente']) && $_SESSION['email_cliente']) {
                EmailHelper::enviarEmailConfirmacao($pedido, $produtos, $_SESSION['email_cliente']);
            }

            unset($_SESSION['carrinho'], $_SESSION['cupom']);

            echo json_encode([
                'status' => 'success',
                'message' => 'Pedido finalizado com sucesso!',
                'pedido_id' => $pedidoId,
                'redirect' => "/montink/pedido/sucesso?id={$pedidoId}"
            ]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erro ao finalizar compra: ' . $e->getMessage()]);
        }
    }
    public function aplicarCupom()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['codigo'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Código do cupom obrigatório']);
            return;
        }

        $codigo = trim($data['codigo']);
        $cupom = $this->cupomModel->validarCodigo($codigo);

        if (!$cupom) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Cupom inválido']);
            return;
        }

        $_SESSION['cupom'] = $cupom;

        echo json_encode(['status' => 'success', 'message' => 'Cupom aplicado com sucesso', 'cupom' => $cupom]);
    }
    private function calcularFrete(float $subtotal): float
    {
        if ($subtotal > 200) {
            return 0.00;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
    public function removerItem()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $produtoId = $data['produto_id'] ?? null;
        $variacaoId = $data['variacao_id'] ?? null;

        if (!$produtoId || !$variacaoId) {
            http_response_code(400);
            echo json_encode(['erro' => 'Produto e variação são obrigatórios']);
            return;
        }

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        foreach ($_SESSION['carrinho'] as $index => $item) {
            if ($item['produto_id'] == $produtoId && $item['variacao_id'] == $variacaoId) {
                unset($_SESSION['carrinho'][$index]);
                break;
            }
        }

        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);

        echo json_encode(['sucesso' => true]);
    }
}
