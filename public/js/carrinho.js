import { mostrarMensagem } from './helpers/mensagens.js';
import { setupCep } from './cep.js';

// Variáveis globais
let produtoIdGlobal, variacaoIdGlobal, estoqueRealGlobal;

// Utilitários
const getNumeroFormatado = (valor) => `R$ ${valor.toFixed(2).replace('.', ',')}`;

const getValorNumerico = (id) => {
    const el = document.getElementById(id);
    if (!el) return 0;
    return parseFloat(el.textContent.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
};

// Ações do carrinho
export function comprarProduto(produtoId, variacaoId, estoque) {
    produtoIdGlobal = produtoId;
    variacaoIdGlobal = variacaoId;
    estoqueRealGlobal = estoque;

    document.getElementById('inputQuantidade').value = '1';
    document.getElementById('estoqueInfo').innerText = `Estoque disponível: ${estoque} unidades`;

    const modal = new bootstrap.Modal(document.getElementById('modalComprar'));
    modal.show();
}

export async function adicionarAoCarrinho() {
    const input = document.getElementById('inputQuantidade');
    const quantidade = parseInt(input.value);

    if (!quantidade || isNaN(quantidade) || quantidade <= 0 || quantidade > estoqueRealGlobal) {
        mostrarMensagem('danger', 'Quantidade inválida ou acima do estoque');
        return;
    }

    try {
        const response = await fetch('/montink/carrinho/adicionar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                produto_id: produtoIdGlobal,
                variacao_id: variacaoIdGlobal,
                quantidade
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            mostrarMensagem('success', 'Produto adicionado ao carrinho!');
            setTimeout(() => window.location.href = '/montink/carrinho', 1000);
        } else {
            mostrarMensagem('danger', data.message || 'Erro ao adicionar ao carrinho');
        }
    } catch (err) {
        console.error(err);
        mostrarMensagem('danger', 'Erro ao conectar com o servidor');
    }
}

export async function removerDoCarrinho(produtoId, variacaoId) {
    if (!produtoId || !variacaoId) {
        console.error('ProdutoId ou VariacaoId inválidos:', { produtoId, variacaoId });
        alert('Erro: produto ou variação inválidos para remoção.');
        return;
    }

    try {
        const response = await fetch('/montink/carrinho/remover', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ produto_id: produtoId, variacao_id: variacaoId }),
        });

        const data = await response.json();

        if (!data.sucesso) {
            alert(data.mensagem || 'Erro ao remover item');
            return;
        }

        window.location.href = '/montink/carrinho';

    } catch (err) {
        console.error('Erro ao remover item:', err);
        alert('Erro ao remover item do carrinho.');
    }
}

// Cupom
export async function aplicarCupom() {
    const input = document.getElementById('cupom_codigo');
    const codigo = input.value.trim();

    if (!codigo) {
        mostrarMensagem('danger', 'Por favor, digite um código de cupom');
        return;
    }

    try {
        const response = await fetch('/montink/api/cupom/validar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ codigo })
        });

        const result = await response.json();

        if (result.status === 'success') {
            const { cupom, desconto, total_com_desconto } = result;

            document.getElementById('cupom-aplicado').textContent = cupom.codigo;
            document.getElementById('cupom-info').classList.remove('d-none');

            const subtotalElement = document.getElementById('valor-total');
            const freteElement = document.getElementById('valor-frete');
            const totalGeralElement = document.getElementById('valor-total-geral');

            let subtotalOriginal = parseFloat(subtotalElement.dataset.original ?? subtotalElement.textContent.replace(/[^\d,]/g, '').replace(',', '.'));
            subtotalElement.dataset.original = subtotalOriginal;

            subtotalElement.textContent = getNumeroFormatado(subtotalOriginal);
            document.getElementById('valor-desconto').textContent = `- ${getNumeroFormatado(desconto)}`;
            document.getElementById('linha-desconto').classList.remove('d-none');

            const frete = parseFloat(freteElement.textContent.replace(/[^\d,]/g, '').replace(',', '.'));

            totalGeralElement.textContent = getNumeroFormatado(total_com_desconto + frete);

            sessionStorage.setItem('cupom', JSON.stringify(cupom));

            mostrarMensagem('success', `Cupom "${cupom.codigo}" aplicado com sucesso!`);
        } else {
            mostrarMensagem('danger', result.message);
        }
    } catch (err) {
        console.error(err);
        mostrarMensagem('danger', 'Erro ao validar cupom');
    }
}

// Finalizar compra
async function finalizarCompra() {
    const form = document.getElementById('form-finalizar-compra');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    const cep = document.getElementById('cep').value.trim();
    const numero = document.getElementById('numero').value.trim();

    if (!cep) {
        mostrarMensagem('danger', 'Por favor, preencha o CEP.');
        document.getElementById('cep').focus();
        return;
    }

    if (!numero) {
        mostrarMensagem('danger', 'Por favor, preencha o número.');
        document.getElementById('numero').focus();
        return;
    }

    data.cep = cep;
    data.numero = numero || 'S/N';

    try {
        const cupom = JSON.parse(sessionStorage.getItem('cupom') || '{}');
        if (cupom?.id) data.cupom_id = cupom.id;
    } catch {
        data.cupom_id = null;
    }

    const complemento = data.complemento ? ` - ${data.complemento}` : '';
    const bairro = data.bairro ? ` - ${data.bairro}` : '';

    data.endereco_completo = `${data.rua}, ${data.numero}${complemento}${bairro} - ${data.cidade}/${data.uf}`;
    document.getElementById('endereco_completo').value = data.endereco_completo;

    data.subtotal = getValorNumerico('valor-total');
    data.frete = getValorNumerico('valor-frete');
    data.desconto = getValorNumerico('valor-desconto');
    data.total = getValorNumerico('valor-total-geral');

    if (!data.endereco_completo) {
        mostrarMensagem('danger', 'Por favor, preencha todos os campos obrigatórios.');
        return;
    }

    try {
        const response = await fetch('/montink/carrinho/finalizar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.status === 'success' && result.redirect) {
            window.location.href = result.redirect;
        } else if (result.status === 'dev' && result.view_url) {
            window.open(result.view_url, '_blank');
            window.location.href = result.redirect || '/';
        } else {
            mostrarMensagem('danger', result.message || 'Erro ao finalizar compra');
        }

    } catch (err) {
        console.error(err);
        mostrarMensagem('danger', 'Erro ao conectar com o servidor');
    }
}

// Eventos DOM
document.addEventListener('DOMContentLoaded', () => {
    setupCep();

    const acoes = [
        { id: 'btn-confirmar-carrinho', fn: adicionarAoCarrinho },
        { id: 'btn-aplicar-cupom', fn: aplicarCupom },
        { id: 'btn-finalizar-compra', fn: finalizarCompra }
    ];

    acoes.forEach(({ id, fn }) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', fn);
    });

    document.querySelectorAll('.btn-excluir-item').forEach(botao => {
        botao.addEventListener('click', (e) => {
            const tr = e.target.closest('tr');
            if (!tr) return;

            const produtoId = parseInt(tr.dataset.produtoId);
            const variacaoId = parseInt(tr.dataset.variacaoId);

            if (produtoId && variacaoId) {
                removerDoCarrinho(produtoId, variacaoId);
            } else {
                alert('Erro: produto ou variação inválidos para remoção.');
            }
        });
    });
});
