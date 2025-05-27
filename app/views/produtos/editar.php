<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Editar Produto</h2>
</div>

<form id="form-produto" class="bg-white p-4 shadow-sm rounded mb-4" data-id="<?= $produto[0]['id'] ?>">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" id="nome" class="form-control" value="<?= htmlspecialchars($produto[0]['nome']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="preco" class="form-label">Preço:</label>
            <input type="number" step="0.01" id="preco" class="form-control" value="<?= $produto[0]['preco'] ?>" required>
        </div>
    </div>

    <h4 class="mt-4">Variações</h4>
    <div id="variacoes-container">
        <?php foreach ($produto as $p): ?>
            <div class="row mb-2 align-items-center variacao-row" data-variacao-id="<?= $p['variacao_id'] ?>">
                <div class="col-md-6">
                    <input type="text" class="form-control variacao-descricao" value="<?= htmlspecialchars($p['descricao']) ?>" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control variacao-estoque" value="<?= $p['quantidade'] ?>" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remover">✖</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-12 d-flex justify-content-center gap-2">
        <button type="submit" id="btn-editar" class="btn btn-success">Salvar</button>
        <a href="/montink" class="btn btn-secondary">Voltar</a>
    </div>
</form>


<script type="module" src="/montink/public/js/formulario/editarProduto.js"></script>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../layout.php';
