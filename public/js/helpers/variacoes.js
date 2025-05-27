export function adicionarVariacao() {
    const container = document.getElementById('variacoes-container');
    const div = document.createElement('div');
    div.className = 'row mb-2 variacao-row';

    div.innerHTML = `
        <div class="col-7">
            <input type="text" class="form-control variacao-descricao" placeholder="Descrição (ex: Azul - M)" required>
        </div>
        <div class="col-4">
            <input type="number" class="form-control variacao-estoque" placeholder="Quantidade em estoque" required>
        </div>
        <div class="col-1 text-center">
            <button type="button" class="btn btn-danger btn-sm btn-remover">X</button>
        </div>
    `;

    container.appendChild(div);

    const btnRemover = div.querySelector('.btn-remover');
    btnRemover.addEventListener('click', () => {
        removerVariacao(btnRemover);
    });
}

export function removerVariacao(btn) {
    btn.closest('.row').remove();
}

document.addEventListener('DOMContentLoaded', () => {
    const btnAdicionar = document.getElementById('btn-adicionar-variacao');
    if (btnAdicionar) {
        btnAdicionar.addEventListener('click', adicionarVariacao);
    }
});
