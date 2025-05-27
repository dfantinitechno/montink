<?php

function calcularSubtotal(array $carrinho, $produtoModel)
{
    $total = 0;
    foreach ($carrinho as $item) {
        $produto = $produtoModel->buscarComVaricoesEEstoque($item['produto_id']);

        $encontrado = null;
        foreach ($produto as $p) {
            if ($p['variacao_id'] == $item['variacao_id']) {
                $encontrado = $p;
                break;
            }
        }

        if (!$encontrado) {
            throw new Exception("Produto ou variação não encontrados");
        }

        $total += $encontrado['preco'] * $item['quantidade'];
    }

    return $total;
}

function validarEstoque(array $carrinho, $pdo)
{
    $stmtEstoqueAtual = $pdo->prepare("SELECT quantidade FROM estoque WHERE variacao_id = ?");

    foreach ($carrinho as $item) {
        $stmtEstoqueAtual->execute([$item['variacao_id']]);
        $dados = $stmtEstoqueAtual->fetch(PDO::FETCH_ASSOC);

        if (!$dados || $dados['quantidade'] < $item['quantidade']) {
            throw new Exception("Estoque insuficiente para variação ID {$item['variacao_id']}");
        }
    }
}

function salvarItensPedido(int $pedido_id, array $produtosEncontrados, $pdo): void
{
    $stmtItem = $pdo->prepare("
        INSERT INTO pedido_produtos 
            (pedido_id, variacao_id, quantidade, preco_unitario)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($produtosEncontrados as $item) {
        $stmtItem->execute([
            $pedido_id,
            $item['variacao_id'],
            $item['quantidade'],
            $item['preco']
        ]);
    }
}

function atualizarEstoque(array $carrinho, $pdo)
{
    $stmtEstoque = $pdo->prepare("
        UPDATE estoque SET quantidade = quantidade - ? WHERE variacao_id = ?
    ");

    foreach ($carrinho as $item) {
        $stmtEstoque->execute([$item['quantidade'], $item['variacao_id']]);
    }
}
