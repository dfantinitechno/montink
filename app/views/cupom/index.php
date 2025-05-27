<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">Cupons Disponíveis</h2>
    <a href="/montink/cupom/novo" class="btn btn-primary fw-bold shadow-sm">Novo Cupom</a>
</div>

<table class="table table-bordered table-striped table-hover shadow-sm rounded text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Percentual</th>
            <th>Subtotal Mínimo</th>
            <th>Validade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($cupons)): ?>
            <tr>
                <td colspan="8" class="text-center">Nenhum cupom encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cupons as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['id']) ?></td>
                    <td><?= htmlspecialchars($c['codigo']) ?></td>
                    <td><?= htmlspecialchars($c['tipo']) ?></td>
                    <td><?= $c['valor'] ? 'R$ ' . number_format($c['valor'], 2, ',', '.') : '-' ?></td>
                    <td><?= $c['percentual'] ? $c['percentual'] . '%' : '-' ?></td>
                    <td>R$ <?= number_format($c['minimo_subtotal'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($c['validade'])) ?></td>
                    <td>
                        <a href="/montink/cupom/editar?id=<?= $c['id'] ?>" class="btn btn-sm btn-primary me-1">Editar</a>
                        <button class="btn btn-sm btn-danger btn-excluir" data-id="<?= $c['id'] ?>">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script type="module" src="/montink/public/js/cupom.js"></script>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../../../app/views/layout.php';
?>