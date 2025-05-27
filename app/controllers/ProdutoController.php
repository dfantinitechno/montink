<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../models/Variacao.php';

class ProdutoController
{
    private $produto;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/conexao.php';
        $pdo = conexao();
        $this->produto = new Produto($pdo);
    }

    public function criar()
    {
        $produto = [];
        $acao = 'cadastrar';
        include __DIR__ . '/../views/produtos/cadastrar.php';
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo "ID inválido";
            exit;
        }

        $produto = $this->produto->buscarComVariacoesEEstoque($id);

        if (!$produto) {
            http_response_code(404);
            echo "Produto não encontrado";
            exit;
        }

        $acao = 'editar';

        include __DIR__ . '/../views/produtos/editar.php';
    }
}
