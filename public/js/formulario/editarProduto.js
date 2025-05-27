import { mostrarMensagem } from '../helpers/mensagens.js';
import { salvarProduto } from '../formulario/salvarProduto.js';

export function editarProduto(produtoId) {
    if (!produtoId || isNaN(produtoId)) {
        alert("ID do produto inválido");
        return;
    }
    window.location.href = '/montink/produto/editar?id=' + produtoId;
}

export async function carregarProdutoParaEdicao(produtoId) {
    try {
        const response = await fetch(`/montink/api/produto/editar/${produtoId}`);
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

        const data = await response.json();
        if (data.status === 'success' && data.data) {
            console.log(data);
            preencherFormulario(data.data);
        } else {
            mostrarMensagem('danger', 'Erro ao carregar dados do produto');
        }
    } catch (err) {
        console.error(err);
        mostrarMensagem('danger', 'Erro ao se comunicar com o servidor');
    }
}

function preencherFormulario(produto) {
    document.getElementById('nome').value = produto.nome || '';
    document.getElementById('preco').value = produto.preco || '';

    const container = document.getElementById('variacoes-container');
    container.innerHTML = '';

    if (produto.variacoes && produto.variacoes.length) {
        produto.variacoes.forEach(v => {
            const div = document.createElement('div');
            div.className = 'row mb-2 align-items-center variacao-row';
            div.dataset.variacaoId = v.id || '';

            div.innerHTML = `
                <div class="col-md-6">
                    <input type="text" class="form-control variacao-descricao" value="${v.descricao || ''}" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control variacao-estoque" value="${v.quantidade || 0}" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remover">✖</button>
                </div>
            `;

            container.appendChild(div);

            div.querySelector('.btn-remover').addEventListener('click', () => {
                div.remove();
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-produto');
    if (form) {
        const produtoId = form.dataset.id;
        if (produtoId) {
            carregarProdutoParaEdicao(produtoId);
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            await salvarProduto();
        });
    }
});
