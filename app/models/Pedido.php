<?php
class PedidoModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function atualizarStatus(int $pedido_id, string $novo_status): bool
    {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $pedido_id]);
        return $stmt->rowCount() > 0;
    }
    public function removerPedido(int $pedido_id): bool
    {
        $stmtItens = $this->pdo->prepare("DELETE FROM pedido_produtos WHERE pedido_id = ?");
        $stmtItens->execute([$pedido_id]);
        $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt->execute([$pedido_id]);
        return $stmt->rowCount() > 0;
    }
    public function pedidoExiste(int $pedido_id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE id = ?");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchColumn() > 0;
    }
}
