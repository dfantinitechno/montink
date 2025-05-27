<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Produto.php';
require_once __DIR__ . '/../../models/Variacao.php';
require_once __DIR__ . '/../../models/Estoque.php';

class ProdutoApiController
{
    private $pdo;
    private $produto;
    private $variacao;
    private $estoque;

    public function __construct()
    {
        $this->pdo = conexao();
        $this->produto = new Produto($this->pdo);
        $this->variacao = new Variacao($this->pdo);
        $this->estoque = new Estoque($this->pdo);
    }

    public function index(): void
    {
        try {
            $produtos = $this->produto->listarComEstoque();
            $this->response(200, 'success', ['data' => $produtos]);
        } catch (Exception $e) {
            $this->response(500, 'error', ['message' => 'Erro ao recuperar produtos.']);
        }
    }


    public function store(): void
    {
        $data = $this->getJsonInput();

        if (!$data || empty($data['nome']) || !isset($data['preco'])) {
            $this->response(400, 'error', ['message' => 'Dados inválidos']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            $this->produto->cadastrar($data['nome'], $data['preco']);
            $produto_id = $this->produto->ultimoId();

            $variacoes = $data['variacoes'] ?? [];
            $this->processarVariacoes($produto_id, $variacoes);

            $this->pdo->commit();

            $this->response(201, 'success', ['produto_id' => $produto_id]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->response(500, 'error', ['message' => $e->getMessage()]);
        }
    }

    public function editar(int $id): void
    {
        try {
            $produto = $this->produto->buscar($id);
            if (!$produto) {
                $this->response(404, 'error', ['message' => 'Produto não encontrado']);
                return;
            }

            $variacoes = $this->variacao->listarPorProduto($id);
            foreach ($variacoes as &$v) {
                $estoque = $this->estoque->buscarPorVariacao($v['id']);
                $v['quantidade'] = $estoque ? (int)$estoque['quantidade'] : 0;
            }
            $produto['variacoes'] = $variacoes;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $this->getJsonInput();

                if (!$data || empty($data['nome']) || !isset($data['preco'])) {
                    $this->response(400, 'error', ['message' => 'Dados inválidos']);
                    return;
                }

                $this->pdo->beginTransaction();

                $this->produto->atualizar($id, $data['nome'], $data['preco']);
                $variacoes = $data['variacoes'] ?? [];
                $this->processarVariacoes($id, $variacoes);

                $this->pdo->commit();

                $this->response(200, 'success', ['message' => 'Produto atualizado com sucesso']);
                return;
            }

            $this->response(200, 'success', ['data' => $produto]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->response(500, 'error', ['message' => $e->getMessage()]);
        }
    }

    public function buscarProdutoComId(int $id): void
    {
        try {
            $produto = $this->produto->buscar($id);
            if (!$produto) {
                $this->response(404, 'error', ['message' => 'Produto não encontrado']);
                return;
            }

            $variacoes = $this->variacao->listarPorProduto($id);
            foreach ($variacoes as &$v) {
                $estoque = $this->estoque->buscarPorVariacao($v['id']);
                $v['quantidade'] = $estoque ? (int)$estoque['quantidade'] : 0;
            }
            $produto['variacoes'] = $variacoes;

            $this->response(200, 'success', ['data' => $produto]);
        } catch (Exception $e) {
            $this->response(500, 'error', ['message' => $e->getMessage()]);
        }
    }

    public function atualizarProdutoComId(int $id): void
    {
        $data = $this->getJsonInput();

        if (!$data || empty($data['nome']) || !isset($data['preco'])) {
            $this->response(400, 'error', ['message' => 'Dados inválidos']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            $this->produto->atualizar($id, $data['nome'], $data['preco']);
            $variacoes = $data['variacoes'] ?? [];
            $this->processarVariacoes($id, $variacoes);

            $this->pdo->commit();

            $this->response(200, 'success', ['message' => 'Produto atualizado com sucesso']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->response(500, 'error', ['message' => $e->getMessage()]);
        }
    }

    private function processarVariacoes(int $produtoId, array $variacoes): void
    {
        foreach ($variacoes as $v) {
            $variacao_id = isset($v['id']) ? (int)$v['id'] : null;
            $descricao = isset($v['descricao']) ? $v['descricao'] : '';
            $quantidade = isset($v['quantidade']) ? (int)$v['quantidade'] : 0;

            if (!$descricao) {
                continue;
            }

            if ($variacao_id) {
                $this->variacao->atualizar($variacao_id, $descricao);
                $this->estoque->atualizar($variacao_id, $quantidade);
            } else {
                $nova_variacao_id = $this->variacao->cadastrar($produtoId, $descricao);
                if ($nova_variacao_id) {
                    $this->estoque->cadastrar($nova_variacao_id, $quantidade);
                }
            }
        }
    }

    private function getJsonInput(): ?array
    {
        $input = file_get_contents('php://input');
        if (!$input) {
            return null;
        }
        $data = json_decode($input, true);
        return is_array($data) ? $data : null;
    }

    private function response(int $httpCode, string $status, array $payload = []): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode(array_merge(['status' => $status], $payload));
    }
}
