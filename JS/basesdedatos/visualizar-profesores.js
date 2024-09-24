// Código tomado de añadir registro editado a visualizar
function mostrarFormularioVisualizar() {
    document.getElementById('modal-visualizar').style.display = 'block';
}

function cerrarFormularioVisualizar() {
    document.getElementById('modal-visualizar').style.display = 'none';
}

// Cerrar el modal al hacer clic en la X
document.querySelector('.close').onclick = function() {
    cerrarFormularioVisualizar();
}

// Cerrar el modal al hacer clic fuera de él
window.onclick = function(event) {
    if (event.target == document.getElementById('modal-visualizar')) {
        cerrarFormularioVisualizar();
    }
}

// js by Cass
// Para que el Modal se pueda visualizar: 

function visualizarInformacionProfesores() {

document.getElementById('modal-visualizar').style.display = 'block';
}

function cerrarModalVisualizar() {
    document.getElementById('modal-visualizar').style.display = 'none';
}