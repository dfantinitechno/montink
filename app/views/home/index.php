<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-dark">Lista de Produtos</h2>
    <a href="/montink/produto/novo" class="btn btn-primary fw-bold shadow-sm">Novo Produto</a>
</div>

<table class="table table-bordered table-striped table-hover shadow-sm rounded text-center align-middle" id="tabela-produtos">
    <thead>
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 25%;">Nome</th>
            <th style="width: 15%;">Preço</th>
            <th style="width: 20%;">Variação</th>
            <th style="width: 10%;">Estoque</th>
            <th style="width: 25%;">Ações</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<div class="modal fade" id="modalComprar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Comprar Produto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="inputQuantidade" class="form-label">Quantidade:</label>
                    <input type="number" class="form-control" id="inputQuantidade" min="1" value="1">
                    <small class="text-muted" id="estoqueInfo"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success fw-semibold" id="btn-confirmar-carrinho">Confirmar</button>
            </div>
        </div>
    </div>
</div>


<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../layout.php';
