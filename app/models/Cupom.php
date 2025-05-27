<?php

class Cupom
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function validarCodigo(string $codigo): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cupons WHERE LOWER(codigo) = :codigo LIMIT 1");
        $stmt->execute(['codigo' => strtolower($codigo)]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function aplicavelAoSubtotal(array $cupom, float $subtotal): bool
    {
        return $cupom['minimo_subtotal'] <= $subtotal;
    }
    public function calcularDesconto(array $cupom, float $subtotal): float
    {
        switch ($cupom['tipo']) {
            case 'valor':
                return min((float) $cupom['valor'], $subtotal);

            case 'percentual':
                return $subtotal * ((float) $cupom['percentual'] / 100);

            default:
                return 0.0;
        }
    }
    public function buscarProdutoComVariacao(int $produto_id, int $variacao_id): ?array
    {
        $sql = "
            SELECT 
                p.id AS produto_id, 
                p.nome, 
                p.preco, 
                v.descricao AS variacao
            FROM produtos p
            JOIN variacoes v ON p.id = v.produto_id
            WHERE p.id = :produto_id AND v.id = :variacao_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'produto_id' => $produto_id,
            'variacao_id' => $variacao_id
        ]);

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $produto ?: null;
    }
    public function calcularSubtotalDoCarrinho(array $carrinho): float
    {
        $subtotal = 0.0;

        foreach ($carrinho as $item) {
            $produto = $this->buscarProdutoComVariacao(
                (int) $item['produto_id'],
                (int) $item['variacao_id']
            );

            if ($produto) {
                $subtotal += $produto['preco'] * (int) $item['quantidade'];
            }
        }

        return $subtotal;
    }
    public function listarAtivos(): array
    {
        $sql = "SELECT * FROM cupons WHERE validade >= NOW() ORDER BY validade ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cupons WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $cupom = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cupom ?: null;
    }
    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM cupons WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
    public function salvar(array $dados): bool
    {
        $sql = "
            INSERT INTO cupons 
                (codigo, tipo, valor, percentual, validade, minimo_subtotal) 
            VALUES 
                (:codigo, :tipo, :valor, :percentual, :validade, :minimo_subtotal)
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'codigo' => $dados['codigo'],
            'tipo' => $dados['tipo'],
            'valor' => $dados['valor'] ?? null,
            'percentual' => $dados['percentual'] ?? null,
            'validade' => $dados['validade'],
            'minimo_subtotal' => $dados['minimo_subtotal'] ?? 0,
        ]);
    }
    public function atualizar(array $dados): bool
    {
        $sql = "
            UPDATE cupons SET
                codigo = :codigo,
                tipo = :tipo,
                valor = :valor,
                percentual = :percentual,
                validade = :validade,
                minimo_subtotal = :minimo_subtotal
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'codigo' => $dados['codigo'],
            'tipo' => $dados['tipo'],
            'valor' => $dados['valor'] ?? null,
            'percentual' => $dados['percentual'] ?? null,
            'validade' => $dados['validade'],
            'minimo_subtotal' => $dados['minimo_subtotal'] ?? 0,
            'id' => $dados['id'],
        ]);
    }
}
