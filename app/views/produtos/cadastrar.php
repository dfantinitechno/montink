<?php
ob_start();
?>

<h2>Cadastrar Produto</h2>

<form id="form-produto">
    <div class="row">
        <div class="col-6 mb-3">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" class="form-control" required>
        </div>
        <div class="col-6 mb-3">
            <label for="preco">Preço:</label>
            <input type="number" step="0.01" id="preco" class="form-control" required>
        </div>
    </div>

    <h4>Variações</h4>
    <div id="variacoes-container">
        <div class="row mb-2 variacao-row">
            <div class="col-7">
                <input type="text" class="form-control variacao-descricao" placeholder="Descrição (ex: Azul - M)" required>
            </div>
            <div class="col-4">
                <input type="number" class="form-control variacao-estoque" placeholder="Quantidade em estoque" required>
            </div>
            <div class="col-1 text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removerVariacao(this)">X</button>
            </div>
        </div>
    </div>
    <button type="button" id="btn-adicionar-variacao" class="btn btn-secondary mb-3">Adicionar Variação</button>
    <hr>

    <div class="col-12 d-flex justify-content-center gap-2">
        <button type="button" id="btn-salvar" class="btn btn-success">Salvar</button>
        <a href="/montink" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<script type="module" src="/montink/public/js/formulario/salvarProduto.js"></script>
<script type="module" src="/montink/public/js/helpers/variacoes.js"></script>

<?php
$conteudo = ob_get_clean();
include __DIR__ . '/../layout.php';
