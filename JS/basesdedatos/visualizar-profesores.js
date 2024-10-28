function visualizarInformacionProfesores() {
    document.getElementById('modal-visualizar').style.display = 'block';
}

function cerrarModalVisualizar() {
    document.getElementById('modal-visualizar').style.display = 'none';
}

// Cerrar el modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('modal-visualizar');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Funcionalidad básica de búsqueda (frontend only)
document.getElementById('buscar-profesor').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    // Aquí irían las funciones de búsqueda
    console.log('Buscando:', searchTerm);
});