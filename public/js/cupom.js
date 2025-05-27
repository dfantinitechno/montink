import { mostrarMensagem } from './helpers/mensagens.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-cupom');
    if (form) {
        console.log("Form encontrado");
        let cupomId = null;

        if (form.dataset.id && !isNaN(form.dataset.id)) {
            cupomId = parseInt(form.dataset.id);
        } else {
            const match = form.action.match(/\/(\d+)$/);
            if (match && match[1] && !isNaN(match[1])) {
                cupomId = parseInt(match[1]);
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const codigo = document.getElementById('codigo').value.trim();
            const tipo = document.getElementById('tipo').value;
            const valor = parseFloat(document.getElementById('valor').value);
            const percentual = parseFloat(document.getElementById('percentual').value);
            const minimoSubtotal = parseFloat(document.getElementById('minimo_subtotal').value);
            const validade = document.getElementById('validade')?.value ?? null;

            let payload = {
                codigo,
                tipo,
                minimo_subtotal: minimoSubtotal,
                validade
            };

            if (tipo === 'valor' && !isNaN(valor)) {
                payload.valor = valor;
                payload.percentual = null;
            } else if (tipo === 'percentual' && !isNaN(percentual)) {
                payload.percentual = percentual;
                payload.valor = null;
            } else {
                mostrarMensagem('danger', 'Preencha corretamente o campo de desconto');
                return;
            }

            if (!payload.codigo || isNaN(minimoSubtotal)) {
                mostrarMensagem('danger', 'Preencha todos os campos obrigatórios');
                return;
            }

            try {
                const url = cupomId
                    ? `/montink/api/cupom/${cupomId}`
                    : '/montink/api/cupom';

                const metodo = cupomId ? 'PUT' : 'POST';

                const resposta = await fetch(url, {
                    method: metodo,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const contentType = resposta.headers.get('content-type');

                if (!contentType || !contentType.includes('application/json')) {
                    const texto = await resposta.text();
                    console.error("Resposta não é JSON:", texto);
                    mostrarMensagem('danger', 'Erro inesperado: resposta inválida do servidor');
                    return;
                }

                const data = await resposta.json();

                if (data.status === 'success') {
                    mostrarMensagem('success', data.message);
                    setTimeout(() => window.location.href = '/montink/cupom', 1500);
                } else {
                    mostrarMensagem('danger', data.message || 'Erro ao salvar');
                }

            } catch (err) {
                console.error("Erro ao salvar", err);
                mostrarMensagem('danger', 'Erro ao conectar com o servidor');
            }
        });

        const tipoSelect = document.getElementById('tipo');
        const inputValor = document.getElementById('valor');
        const inputPercentual = document.getElementById('percentual');

        function toggleFields() {
            if (tipoSelect.value === 'valor') {
                inputValor.disabled = false;
                inputPercentual.disabled = true;
            } else if (tipoSelect.value === 'percentual') {
                inputValor.disabled = true;
                inputPercentual.disabled = false;
            } else {
                inputValor.disabled = false;
                inputPercentual.disabled = false;
            }
        }
        toggleFields();
        tipoSelect.addEventListener('change', toggleFields);

        form.addEventListener('submit', (e) => {
            const valor = parseFloat(inputValor.value);
            const percentual = parseFloat(inputPercentual.value);

            if (isNaN(valor) && isNaN(percentual)) {
                alert("Preencha pelo menos um dos campos: Valor ou Percentual");
                e.preventDefault();
            }

            if (!isNaN(valor) && !isNaN(percentual)) {
                alert("Escolha apenas um tipo de desconto: Valor ou Percentual");
                e.preventDefault();
            }
        });
    }

    const botoesExcluir = document.querySelectorAll('.btn-excluir');
    console.log(`Botões excluir encontrados: ${botoesExcluir.length}`);

    botoesExcluir.forEach(botao => {
        botao.addEventListener('click', async () => {
            const id = botao.dataset.id;
            if (!id) return;

            if (!confirm('Confirma exclusão do cupom?')) return;

            try {
                const resposta = await fetch(`/montink/api/cupom/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await resposta.json();

                if (data.status === 'success') {
                    mostrarMensagem('success', data.message);
                    setTimeout(() => {
                        window.location.href = '/montink/cupom';
                    }, 1000);
                } else {
                    mostrarMensagem('danger', data.message || 'Erro ao excluir');
                }
            } catch (err) {
                console.error('Erro ao excluir:', err);
                mostrarMensagem('danger', 'Erro ao conectar com o servidor');
            }
        });
    });
});
