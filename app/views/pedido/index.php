<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">Meus Pedidos</h2>
</div>

<table class="table table-bordered table-striped table-hover shadow-sm rounded text-center align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Subtotal</th>
            <th>Frete</th>
            <th>Desconto</th>
            <th>Total</th>
            <th>Status</th>
            <th>Cupom</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pedidos)): ?>
            <tr>
                <td colspan="7" class="text-center">Nenhum pedido encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></td>
                    <td>R$ <?= number_format($p['subtotal'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($p['frete'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($p['desconto'] ?? 0, 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                    <td>
                        <?= htmlspecialchars($p['status']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($p['cupom_codigo'] ?? '-') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>

</table>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../layout.php';
?>