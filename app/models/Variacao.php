<?php
class Variacao
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function cadastrar(int $produto_id, string $descricao)
    {
        $stmt = $this->pdo->prepare("INSERT INTO variacoes (produto_id, descricao) VALUES (?, ?)");
        if ($stmt->execute([$produto_id, $descricao])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }
    public function atualizar(int $variacao_id, string $descricao): bool
    {
        $stmt = $this->pdo->prepare("UPDATE variacoes SET descricao = ? WHERE id = ?");
        return $stmt->execute([$descricao, $variacao_id]);
    }
    public function listarPorProduto(int $produto_id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM variacoes WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ultimoId()
    {
        return $this->pdo->lastInsertId();
    }
}
