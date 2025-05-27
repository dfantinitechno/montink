<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Cupom.php';

class CupomApiController
{
    private $cupom;

    public function __construct()
    {
        $pdo = conexao();
        $this->cupom = new Cupom($pdo);
    }

    public function validar(): void
    {
        $input = $this->getJsonInput();
        $codigo = $input['codigo'] ?? '';
        $codigo = strtolower(trim($codigo));

        if (empty($codigo)) {
            $this->response(400, 'Código do cupom inválido');
            return;
        }

        $cupom = $this->cupom->validarCodigo($codigo);
        if (!$cupom) {
            $this->response(404, "Cupom '$codigo' não encontrado");
            return;
        }

        $validade = strtotime($cupom['validade']);
        $agora = time();

        if ($validade < $agora) {
            $this->response(400, 'Cupom expirado');
            return;
        }

        $subtotal = $this->calcularSubtotalCarrinho();

        if (!$this->cupom->aplicavelAoSubtotal($cupom, $subtotal)) {
            $this->response(400, 'Subtotal mínimo não atingido');
            return;
        }

        $_SESSION['cupom'] = $cupom;

        $desconto = $this->cupom->calcularDesconto($cupom, $subtotal);

        $this->response(200, 'Cupom válido', [
            'cupom' => $cupom,
            'desconto' => round($desconto, 2),
            'subtotal' => round($subtotal, 2),
            'total_com_desconto' => round($subtotal - $desconto, 2),
        ]);
    }

    public function listarAtivos(): void
    {
        try {
            $cupons = $this->cupom->listarAtivos();
            $this->response(200, 'Lista de cupons ativos', ['data' => $cupons]);
        } catch (PDOException $e) {
            $this->response(500, 'Erro ao listar cupons ativos');
        }
    }

    public function buscarPorId(int $id): void
    {
        try {
            $cupom = $this->cupom->buscarPorId($id);
            if (!$cupom) {
                $this->response(404, 'Cupom não encontrado');
                return;
            }
            $this->response(200, 'Cupom encontrado', ['data' => $cupom]);
        } catch (PDOException $e) {
            $this->response(500, 'Erro ao buscar cupom');
        }
    }

    public function salvar(): void
    {
        $input = $this->getJsonInput();

        if (empty($input['codigo']) || empty($input['tipo'])) {
            $this->response(400, 'Campos obrigatórios não preenchidos');
            return;
        }

        if ($this->cupom->validarCodigo($input['codigo'])) {
            $this->response(409, 'Código de cupom já existe');
            return;
        }

        try {
            $this->cupom->salvar($input);
            $this->response(200, 'Cupom salvo com sucesso');
        } catch (PDOException $e) {
            $this->response(500, 'Erro ao salvar cupom');
        }
    }

    public function atualizarCupom(int $id): void
    {
        $dados = $this->getJsonInput();
        if (empty($dados)) {
            $dados = $_POST;
        }
        $dados['id'] = $id;

        try {
            $this->cupom->atualizar($dados);
            $this->response(200, 'Cupom atualizado com sucesso');
        } catch (PDOException $e) {
            $this->response(500, 'Erro ao atualizar cupom: ' . $e->getMessage());
        }
    }

    public function excluir(int $id): void
    {
        try {
            $excluido = $this->cupom->excluir($id);
            if ($excluido) {
                $this->response(200, 'Cupom excluído com sucesso');
            } else {
                $this->response(404, 'Cupom não encontrado para exclusão');
            }
        } catch (PDOException $e) {
            $this->response(500, 'Erro ao excluir cupom');
        }
    }

    private function response(int $statusCode, string $message, array $extra = []): void
    {
        http_response_code($statusCode);
        echo json_encode(array_merge(['status' => $statusCode < 400 ? 'success' : 'error', 'message' => $message], $extra));
    }

    private function getJsonInput(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    private function calcularSubtotalCarrinho(): float
    {
        $subtotal = 0.0;
        if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
            return $subtotal;
        }

        foreach ($_SESSION['carrinho'] as $item) {
            $produto = $this->cupom->buscarProdutoComVariacao($item['produto_id'], $item['variacao_id']);
            if ($produto) {
                $subtotal += $produto['preco'] * $item['quantidade'];
            }
        }

        return $subtotal;
    }
}
