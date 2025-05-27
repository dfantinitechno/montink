export function setupCep() {
    const cepInput = document.getElementById('cep');
    if (!cepInput) return;

    cepInput.addEventListener('blur', (e) => {
        const valor = e.target.value;
        pesquisacep(valor);
    });
}

function limpa_formulario_cep() {
    document.getElementById('rua')?.setAttribute('value', '');
    document.getElementById('bairro')?.setAttribute('value', '');
    document.getElementById('cidade')?.setAttribute('value', '');
    document.getElementById('uf')?.setAttribute('value', '');
    document.getElementById('endereco_completo')?.setAttribute('value', '');
}

async function pesquisacep(valor) {
    const cep = valor.replace(/\D/g, '');
    if (cep.length !== 8) {
        limpa_formulario_cep();
        alert("Formato de CEP inválido");
        return;
    }

    document.getElementById('rua')?.setAttribute('value', '...');
    document.getElementById('bairro')?.setAttribute('value', '...');
    document.getElementById('cidade')?.setAttribute('value', '...');
    document.getElementById('uf')?.setAttribute('value', '...');

    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        if (!response.ok) throw new Error("Erro na resposta do servidor");

        const conteudo = await response.json();

        if ("erro" in conteudo) {
            limpa_formulario_cep();
            alert("CEP não encontrado");
            return;
        }

        const numero = document.getElementById('numero')?.value.trim() || "S/N";
        const complemento = document.getElementById('complemento')?.value.trim() || "";

        let enderecoCompleto = `${conteudo.logradouro}, ${numero}`;
        if (complemento) enderecoCompleto += ` - Complemento: ${complemento}`;
        enderecoCompleto += ` - ${conteudo.localidade}/${conteudo.uf}`;

        document.getElementById('rua')?.setAttribute('value', conteudo.logradouro || 'Rua não encontrada');
        document.getElementById('bairro')?.setAttribute('value', conteudo.bairro || 'Bairro não encontrado');
        document.getElementById('cidade')?.setAttribute('value', conteudo.localidade || 'Cidade não encontrada');
        document.getElementById('uf')?.setAttribute('value', conteudo.uf || '');

        const inputEndereco = document.getElementById('endereco_completo');
        if (inputEndereco) {
            inputEndereco.value = enderecoCompleto;
        }
    } catch (err) {
        console.error("Erro ao buscar CEP", err);
        limpa_formulario_cep();
        alert("Não foi possível buscar informações do CEP");
    }
}