<?php
class Produto
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function cadastrar(string $nome, float $preco): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
        return $stmt->execute([$nome, $preco]);
    }
    public function ultimoId()
    {
        return $this->pdo->lastInsertId();
    }
    public function listar(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM produtos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function atualizar(int $id, string $nome, float $preco): bool
    {
        $stmt = $this->pdo->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
        return $stmt->execute([$nome, $preco, $id]);
    }
    public function buscar($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function listarComEstoque(): array
    {
        $sql = "SELECT p.*, p.id as produto_id, v.id AS variacao_id, v.descricao AS descricao, e.quantidade
            FROM produtos p
            LEFT JOIN variacoes v ON v.produto_id = p.id
            LEFT JOIN estoque e ON e.variacao_id = v.id
            ORDER BY p.id DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarComVariacoesEEstoque(int $id): array
    {
        $sql = "
        SELECT 
            p.*, 
            v.id AS variacao_id, 
            v.descricao AS descricao, 
            e.quantidade as quantidade
        FROM produtos p
        LEFT JOIN variacoes v ON v.produto_id = p.id
        LEFT JOIN estoque e ON e.variacao_id = v.id
        WHERE p.id = ?
        ORDER BY v.id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarVariacaoComEstoque(int $variacaoId): ?array
    {
        $sql = "
        SELECT 
            v.id AS variacao_id,
            v.descricao,
            p.preco,
            e.quantidade
        FROM variacoes v
        JOIN produtos p ON p.id = v.produto_id
        LEFT JOIN estoque e ON e.variacao_id = v.id
        WHERE v.id = ?
        LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$variacaoId]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }
}
