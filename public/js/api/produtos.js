import { mostrarMensagem } from '../helpers/mensagens.js';
import { editarProduto } from '../formulario/editarProduto.js';
import { comprarProduto } from '../carrinho.js';

export async function carregarProdutos() {
    try {
        const response = await fetch('/montink/api/produto');
        if (!response.ok) throw new Error('Erro na API');

        const data = await response.json();
        const tbody = document.querySelector('#tabela-produtos tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!data.data?.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">Nenhum produto encontrado.</td></tr>`;
            return;
        }

        data.data.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${p.produto_id}</td>
                <td>${p.nome}</td>
                <td>R$ ${parseFloat(p.preco).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td>
                <td>${p.descricao || ''}</td>
                <td>${p.quantidade || 0}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary btn-editar" data-id="${p.produto_id}">
                        <i class="bi bi-pencil-square"></i> Editar
                    </button>
                    <button type="button" class="btn btn-sm btn-primary btn-comprar"
                        data-produto-id="${p.produto_id}"
                        data-variacao-id="${p.variacao_id}"
                        data-quantidade="${p.quantidade}">
                        Comprar
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        tbody.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.currentTarget.dataset.id;
                editarProduto(id);
            });
        });

        tbody.querySelectorAll('.btn-comprar').forEach(btn => {
            btn.addEventListener('click', e => {
                const btnEl = e.currentTarget;
                const produtoId = btnEl.dataset.produtoId;
                const variacaoId = btnEl.dataset.variacaoId;
                const quantidade = btnEl.dataset.quantidade;
                comprarProduto(produtoId, variacaoId, quantidade);
            });
        });

    } catch (err) {
        console.error(err);
        mostrarMensagem('danger', 'Erro ao carregar produtos.');
    }
}
