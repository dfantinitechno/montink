<?php
ob_start();
?>

<h2>Pedido Finalizado com Sucesso!</h2>

<div class="alert alert-success">
    <strong>ID do Pedido:</strong> <?= htmlspecialchars($pedido['id']) ?><br>
    <strong>Status:</strong> <?= htmlspecialchars($pedido['status']) ?><br>
    <strong>Data do Pedido:</strong> <?= date('d/m/Y H:i:s', strtotime($pedido['data_pedido'])) ?>
</div>

<h4>Endereço de Entrega</h4>
<p><?= nl2br(htmlspecialchars($pedido['endereco_completo'])) ?></p>

<h4>Produtos Comprados</h4>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Preço Unitário</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($produtos) && is_array($produtos)): ?>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $p['quantidade'] ?></td>
                    <td>R$ <?= number_format($p['preco_unitario'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($p['subtotal'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center text-muted">Nenhum produto encontrado</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
            <td><strong>R$ <?= number_format($pedido['subtotal'], 2, ',', '.') ?></strong></td>
        </tr>
        <tr>
            <td colspan="3" class="text-end">Frete:</td>
            <td>R$ <?= number_format($pedido['frete'], 2, ',', '.') ?></td>
        </tr>

        <?php if (!empty($pedido['cupom_codigo'])): ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Cupom Aplicado:</strong></td>
                <td><strong><?= htmlspecialchars($pedido['cupom_codigo']) ?></strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-end">Desconto Cupom:</td>
                <td>- R$ <?= number_format($pedido['cupom_valor'], 2, ',', '.') ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="3" class="text-end"><strong>Total:</strong></td>
            <td><strong>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></strong></td>
        </tr>
    </tfoot>
</table>

<a href="/montink/" class="btn btn-primary">Voltar para Home</a>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../layout.php';
?>