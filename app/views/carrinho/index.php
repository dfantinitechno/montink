<?php
ob_start();
?>

<h2>Seu Carrinho</h2>

<form id="form-finalizar-compra" action="/montink/carrinho/finalizar" method="POST">
    <div class="row g-3 mb-3">
        <div class="col-3">
            <label for="cep" class="form-label">CEP (ex: 01001-000)</label>
            <input type="text" name="cep" id="cep" class="form-control" placeholder="Digite seu CEP" required>
        </div>
        <div class="col-9">
            <label for="rua" class="form-label">Rua:</label>
            <input type="text" name="rua" id="rua" class="form-control" readonly>
        </div>

        <div class="col-2">
            <label for="numero" class="form-label">Número:</label>
            <input type="text" name="numero" id="numero" class="form-control" placeholder="Ex: 123" required>
        </div>
        <div class="col-2">
            <label for="bairro" class="form-label">Bairro:</label>
            <input type="text" name="bairro" id="bairro" class="form-control" readonly>
        </div>

        <div class="col-2">
            <label for="cidade" class="form-label">Cidade:</label>
            <input type="text" name="cidade" id="cidade" class="form-control" readonly>
        </div>
        <div class="col-2">
            <label for="uf" class="form-label">UF:</label>
            <input type="text" name="uf" id="uf" class="form-control" readonly>
        </div>
        <div class="col-4">
            <label for="complemento" class="form-label">Complemento:</label>
            <input type="text" name="complemento" id="complemento" class="form-control">
        </div>

        <div class="col-6">
            <label for="email_cliente" class="form-label">Seu E-mail (para confirmação):</label>
            <input type="email" name="email_cliente" id="email_cliente" class="form-control" placeholder="exemplo@dominio.com" required>
        </div>

        <div class="col-6">
            <label for="cupom_codigo" class="form-label">Tem um cupom?</label>
            <div class="input-group">
                <input type="text" id="cupom_codigo" class="form-control" placeholder="Digite o código do cupom">
                <button id="btn-aplicar-cupom" class="btn btn-success" type="button">Aplicar</button>
            </div>

            <small id="cupom-info" class="form-text text-success bg-success bg-opacity-10 border border-success rounded p-2 mt-2 d-none">
                Cupom <strong id="cupom-aplicado"></strong> aplicado!
            </small>
        </div>
    </div>

    <input type="hidden" name="endereco_completo" id="endereco_completo" />

    <?php if (!empty($produtos)): ?>
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Produto</th>
                    <th>Variação</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr data-produto-id="<?= htmlspecialchars($p['produto_id']) ?>"
                        <?= !empty($p['variacao_id']) ? 'data-variacao-id="' . htmlspecialchars($p['variacao_id']) . '"' : '' ?>>

                        <td style="position: relative;">

                            <?= htmlspecialchars($p['nome']) ?>

                            <button
                                class="btn btn-sm btn-danger btn-excluir-item"
                                type="button"
                                title="Remover item do carrinho"
                                aria-label="Remover item do carrinho"
                                style="position: absolute; top: 50%; right: 5px; transform: translateY(-50%); padding: 0 6px; font-weight: bold; font-size: 1rem; line-height: 1; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;">
                                &times;
                            </button>
                        </td>

                        <td><?= htmlspecialchars($p['variacao'] ?? '-') ?></td>
                        <td><?= $p['quantidade'] ?></td>
                        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($p['subtotal'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td><strong id="valor-total"><?= number_format($total, 2, ',', '.') ?></strong></td>
                </tr>
                <tr id="linha-desconto" class="d-none">
                    <td colspan="4" class="text-end text-success"><strong>Desconto Cupom:</strong></td>
                    <td id="valor-desconto">R$ 0,00</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end">Frete:</td>
                    <td id="valor-frete"><?= number_format($frete, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td><strong id="valor-total-geral"><?= number_format($totalGeral, 2, ',', '.') ?></strong></td>
                </tr>

            </tfoot>
        </table>

        <div class="d-flex gap-3">
            <a href="/montink/" class="btn btn-primary flex-grow-1">Continuar Comprando</a>
            <button id="btn-finalizar-compra" type="button" class="btn btn-success flex-grow-1">
                Finalizar Compra
            </button>
        </div>

    <?php else: ?>
        <div class="alert alert-info mt-3">Seu carrinho está vazio.</div>
        <a href="/montink/" class="btn btn-primary">Voltar</a>
    <?php endif; ?>
</form>

<script type="module" src="/montink/public/js/cep.js"></script>
<script type="module" src="/montink/public/js/carrinho.js"></script>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../../../app/views/layout.php';
?>