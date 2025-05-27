<?php
class Estoque
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function cadastrar(int $variacao_id, int $quantidade): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO estoque (variacao_id, quantidade) VALUES (?, ?)");
        return $stmt->execute([$variacao_id, $quantidade]);
    }
    public function atualizar(int $variacao_id, int $quantidade): bool
    {
        $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = ? WHERE variacao_id = ?");
        return $stmt->execute([$quantidade, $variacao_id]);
    }
    public function buscarPorVariacao(int $variacao_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM estoque WHERE variacao_id = ?");
        $stmt->execute([$variacao_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function diminuir(int $variacao_id, int $quantidade): bool
    {
        $stmt = $this->pdo->prepare("
        UPDATE estoque 
        SET quantidade = quantidade - ? 
        WHERE variacao_id = ? AND quantidade >= ?
        ");

        return $stmt->execute([$quantidade, $variacao_id, $quantidade]);
    }
}
