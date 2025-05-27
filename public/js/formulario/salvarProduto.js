import { mostrarMensagem } from '../helpers/mensagens.js';

export async function salvarProduto() {
    const form = document.getElementById('form-produto');
    const produtoId = form.dataset.id;

    const nome = document.getElementById('nome').value.trim();
    const preco = parseFloat(document.getElementById('preco').value);
    const variacoesElems = document.querySelectorAll('.variacao-row');

    let variacoes = [];

    for (let row of variacoesElems) {
        const descricaoInput = row.querySelector('.variacao-descricao');
        const estoqueInput = row.querySelector('.variacao-estoque');

        if (!descricaoInput || !estoqueInput) continue;

        const descricao = descricaoInput.value.trim();
        const quantidade = parseInt(estoqueInput.value);
        const variacaoId = row.dataset.variacaoId;

        if (descricao && !isNaN(quantidade) && quantidade >= 0) {
            variacoes.push({
                id: variacaoId || null,
                descricao,
                quantidade
            });
        }
    }

    if (!nome || isNaN(preco) || variacoes.length === 0) {
        mostrarMensagem('danger', 'Preencha todos os campos.');
        return;
    }

    try {
        const url = produtoId
            ? `/montink/api/produto/editar/${produtoId}`
            : '/montink/api/produto';

        const response = await fetch(url, {
            method: produtoId ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nome, preco, variacoes })
        });

        const data = await response.json();

        if (data.status === 'success') {
            mostrarMensagem('success', 'Produto salvo com sucesso!');
            setTimeout(() => window.location.href = '/montink/', 1500);
        } else {
            mostrarMensagem('danger', data.message || 'Erro ao salvar');
        }

    } catch (err) {
        console.error("Erro no cadastro:", err);
        mostrarMensagem('danger', 'Erro ao conectar com o servidor');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const btnSalvar = document.getElementById('btn-salvar');
    if (btnSalvar) {
        btnSalvar.addEventListener('click', salvarProduto);
    }
});
