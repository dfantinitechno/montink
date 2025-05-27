<?php
ob_start();
?>

<h2><?= isset($cupom['id']) ? 'Editar Cupom #' . $cupom['id'] : 'Cadastrar Cupom' ?></h2>

<form id="form-cupom"
    action="<?= isset($cupom['id']) ? '/montink/api/cupom/' . $cupom['id'] : '/montink/api/cupom' ?>"
    method="POST"
    <?= isset($cupom['id']) ? 'data-id="' . $cupom['id'] . '"' : '' ?>>

    <?php if (isset($cupom['id'])): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row">
        <div class="col-6 mb-3">
            <label for="codigo" class="form-label">Código do Cupom:</label>
            <input type="text" name="codigo" id="codigo" class="form-control"
                value="<?= htmlspecialchars($cupom['codigo'] ?? '') ?>" required>
        </div>

        <div class="col-6 mb-3">
            <label for="tipo" class="form-label">Tipo de Desconto:</label>
            <select name="tipo" id="tipo" class="form-select" required>
                <option value="valor" <?= (isset($cupom['tipo']) && $cupom['tipo'] === 'valor') ? 'selected' : '' ?>>Desconto em Valor</option>
                <option value="percentual" <?= (isset($cupom['tipo']) && $cupom['tipo'] === 'percentual') ? 'selected' : '' ?>>Desconto em Percentual</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="valor" class="form-label">Valor (R$):</label>
            <input type="number" step="0.01" min="0" name="valor" id="valor" class="form-control"
                value="<?= $cupom['valor'] ?? '' ?>"
                <?= isset($cupom['tipo']) && $cupom['tipo'] === 'percentual' ? 'disabled' : '' ?>
                required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="percentual" class="form-label">Percentual (%):</label>
            <input type="number" step="0.01" min="0" max="100" name="percentual" id="percentual" class="form-control"
                value="<?= $cupom['percentual'] ?? '' ?>"
                <?= isset($cupom['tipo']) && $cupom['tipo'] === 'valor' ? 'disabled' : '' ?>
                required>
        </div>

        <div class="col-6 mb-3">
            <label for="minimo_subtotal" class="form-label">Subtotal Mínimo:</label>
            <input type="number" step="0.01" min="0" name="minimo_subtotal" id="minimo_subtotal" class="form-control"
                value="<?= $cupom['minimo_subtotal'] ?? 0 ?>" required>
        </div>

        <div class="col-6 mb-3">
            <label for="validade" class="form-label">Data de Validade:</label>
            <input type="datetime-local" name="validade" id="validade" class="form-control"
                value="<?= isset($cupom['validade']) ? date('Y-m-d\TH:i', strtotime($cupom['validade'])) : '' ?>" required>
        </div>
    </div>

    <div class="col-12 d-flex justify-content-center gap-2">
        <button type="submit" class="btn btn-success"><?= isset($cupom['id']) ? 'Atualizar' : 'Cadastrar' ?></button>
        <a href="/montink/cupom" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<script type="module" src="/montink/public/js/cupom.js"></script>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../../../app/views/layout.php';
?>