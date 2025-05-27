export function mostrarMensagem(tipo, texto) {
    const msgBox = document.getElementById('mensagem');
    if (!msgBox) return;

    msgBox.className = `alert alert-${tipo} d-block`;
    msgBox.textContent = texto;

    setTimeout(() => {
        msgBox.classList.remove('d-block');
        msgBox.textContent = '';
    }, 4000);
}
