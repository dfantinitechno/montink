<?php

require_once __DIR__ . '/../models/Produto.php';
class HomeController
{
    private $produto;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/conexao.php';
        $pdo = conexao();
        $this->produto = new Produto($pdo);
    }

    public function index()
    {
        $produtos = $this->produto->listarComEstoque();
        include __DIR__ . '/../views/home/index.php';
    }
}
