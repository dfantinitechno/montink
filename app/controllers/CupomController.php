<?php

require_once __DIR__ . '/../models/Cupom.php';

class CupomController
{
    private $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/conexao.php';
        $this->pdo = conexao();
    }

    public function index()
    {
        $stmt = $this->pdo->query("SELECT * FROM cupons ORDER BY data_criacao DESC");
        $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/cupom/index.php';
    }

    public function novo()
    {
        $action = "/montink/cupom/salvar";
        include __DIR__ . '/../views/cupom/form.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
    
        if (!$id) {
            header("Location: /montink/cupom");
            exit;
        }
    
        $stmt = $this->pdo->prepare("SELECT * FROM cupons WHERE id = ?");
        $stmt->execute([$id]);
        $cupom = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$cupom) {
            header("Location: /montink/cupom");
            exit;
        }

        $action = "/montink/cupom/atualizar";
        
        include __DIR__ . '/../views/cupom/form.php';
    }

    public function excluir()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) header('Location: /montink/cupom');

        $stmt = $this->pdo->prepare("DELETE FROM cupons WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /montink/cupom");
        exit;
    }
}
