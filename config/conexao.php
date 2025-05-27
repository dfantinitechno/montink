<?php
$host = "localhost";
$dbname = "montink";
$user = "root";
$pass = "";

function conexao()
{
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=montink;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}
