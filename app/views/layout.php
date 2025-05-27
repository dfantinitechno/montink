<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Montink</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="/montink/public/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <div class="sidebar">
            <h4>Montink</h4>
            <ul class="nav flex-column mt-4">
                <li class="nav-item mb-2">
                    <a class="nav-link px-3 py-2" href="/montink/">🏠 Início</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link px-3 py-2" href="/montink/cupom">🏷️ Cupons</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link px-3 py-2" href="/montink/pedidos">📦 Pedidos</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link px-3 py-2" href="/montink/carrinho">🛒 Carrinho</a>
                </li>
            </ul>
        </div>

        <div id="mensagem"></div>
        <main class="content">
            <?= $conteudo ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script type="module" src="/montink/public/js/index.js"></script>

</body>

</html>