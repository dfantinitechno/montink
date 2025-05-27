<?php

class PedidoController
{
    private $pdo;

    public function __construct()
    {
        session_start();

        require_once __DIR__ . '/../../config/conexao.php';
        $this->pdo = conexao();
    }

    public function index()
    {
        try {
            $stmt = $this->pdo->query("
                    SELECT 
                        p.id,
                        p.data_pedido,
                        p.subtotal,
                        p.frete,
                        p.total,
                        p.status,
                        p.desconto,
                        c.codigo AS cupom_codigo
                    FROM pedidos p
                    LEFT JOIN cupons c ON p.cupom_id = c.id
                    ORDER BY p.data_pedido DESC
                ");
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            include __DIR__ . '/../views/pedido/index.php';
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Erro ao carregar pedidos: " . $e->getMessage();
        }
    }

    public function sucesso()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: /montink/");
            exit;
        }

        $stmt = $this->pdo->prepare("
        SELECT p.*, c.codigo AS cupom_codigo, c.valor AS cupom_valor, c.percentual AS cupom_percentual, p.data_pedido
        FROM pedidos p
        LEFT JOIN cupons c ON p.cupom_id = c.id
        WHERE p.id = ?
             ");
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            header("Location: /montink/");
            exit;
        }

        $stmtProdutos = $this->pdo->prepare("
                SELECT 
                    pp.quantidade, 
                    p.nome,
                    v.descricao AS variacao,
                    pp.preco_unitario,
                    pp.quantidade * pp.preco_unitario AS subtotal
                FROM pedido_produtos pp
                JOIN variacoes v ON pp.variacao_id = v.id
                JOIN produtos p ON v.produto_id = p.id
                WHERE pp.pedido_id = ?
            ");
        $stmtProdutos->execute([$id]);
        $produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/pedido/sucesso.php';
    }
}
