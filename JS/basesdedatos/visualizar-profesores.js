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

// Función para cargar los profesores del departamento
async function cargarProfesores(departamentoId) {
    try {
        const response = await fetch(`/api/profesores/${departamentoId}`);
        const profesores = await response.json();
        
        const listaProfesores = document.getElementById('lista-profesores');
        listaProfesores.innerHTML = '';
        
        profesores.forEach(profesor => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${profesor.nombre}</td>
                <td>${profesor.codigo}</td>
                <td>${profesor.tipo_contrato}</td>
            `;
            listaProfesores.appendChild(row);
        });
        
        // Actualizar nombre del departamento
        document.getElementById('nombre-departamento').textContent = 
            `Departamento: ${profesores[0]?.nombre_departamento || ''}`;
            
    } catch (error) {
        console.error('Error al cargar profesores:', error);
    }
}

// Función para filtrar profesores
function filtrarProfesores() {
    const input = document.getElementById('buscar-profesor');
    const filtro = input.value.toLowerCase();
    const filas = document.getElementById('lista-profesores').getElementsByTagName('tr');
    
    for (let fila of filas) {
        const nombre = fila.cells[0].textContent.toLowerCase();
        const codigo = fila.cells[1].textContent.toLowerCase();
        fila.style.display = 
            nombre.includes(filtro) || codigo.includes(filtro) ? '' : 'none';
    }
}

// Función para abrir el modal
function abrirModalProfesores(departamentoId) {
    const modal = document.getElementById('modal-profesores');
    modal.style.display = 'block';
    cargarProfesores(departamentoId);
}

// Función para cerrar el modal
function cerrarModalProfesores() {
    const modal = document.getElementById('modal-profesores');
    modal.style.display = 'none';
}