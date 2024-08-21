const modal = document.getElementById('modal');
const abrirModalBtn = document.getElementById('abrirModal');
const cerrarModalBtn = document.querySelector('.close');

abrirModalBtn.onclick = function(e) {
    e.preventDefault();
    modal.style.display = "flex";
    ajustarTamañoModal();
}

cerrarModalBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function ajustarTamañoModal() {
    const modalContent = document.querySelector('.modal-content');
    const tabla = modalContent.querySelector('table');
    const boton = modalContent.querySelector('#confirmarParticipantes');
    
    modalContent.style.maxHeight = '80vh';
    
    const alturaContenido = tabla.offsetHeight + boton.offsetHeight + 60;
    
    if (alturaContenido < window.innerHeight * 0.8) {
        modalContent.style.maxHeight = `${alturaContenido}px`;
    }
}

window.addEventListener('resize', ajustarTamañoModal);