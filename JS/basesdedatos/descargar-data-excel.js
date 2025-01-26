function mostrarDescargarExcel() {
  document.getElementById('modal-descargar').style.display = 'block';
}

function cerrarDescargarExcel() {
  document.getElementById('modal-descargar').style.display = 'none';
}

// Inicializar los manejadores de checkbox
function initializeCheckboxHandlers() {
  // Manejador para "Seleccionar/Deseleccionar todas"
  const selectAllMain = document.querySelector('.select-first input[type="checkbox"]');
  if (selectAllMain) {
    selectAllMain.addEventListener('change', function() {
      const allCheckboxes = document.querySelectorAll('.columns-container-materia input[type="checkbox"], .columns-container-profesor input[type="checkbox"]');
      allCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      
      // También actualiza los checkboxes de grupo
      const selectAllMateria = document.querySelector('.select-all-materia input[type="checkbox"]');
      const selectAllProfesor = document.querySelector('.select-all-profesor input[type="checkbox"]');
      if (selectAllMateria) selectAllMateria.checked = this.checked;
      if (selectAllProfesor) selectAllProfesor.checked = this.checked;
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Materia"
  const selectAllMateria = document.querySelector('.select-all-materia input[type="checkbox"]');
  if (selectAllMateria) {
    selectAllMateria.addEventListener('change', function() {
      const materiaCheckboxes = document.querySelectorAll('.columns-container-materia input[type="checkbox"]');
      materiaCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Profesorado"
  const selectAllProfesor = document.querySelector('.select-all-profesor input[type="checkbox"]');
  if (selectAllProfesor) {
    selectAllProfesor.addEventListener('change', function() {
      const profesorCheckboxes = document.querySelectorAll('.columns-container-profesor input[type="checkbox"]');
      profesorCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Agregar eventos change a todos los checkboxes individuales
  const allIndividualCheckboxes = document.querySelectorAll('.columns-container-materia input[type="checkbox"], .columns-container-profesor input[type="checkbox"]');
  allIndividualCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      updateGroupCheckboxes();
      updateMainCheckbox();
    });
  });
}

// Actualizar el estado de los checkboxes de grupo
function updateGroupCheckboxes() {
  // Verificar grupo de Materia
  const materiaCheckboxes = Array.from(document.querySelectorAll('.columns-container-materia input[type="checkbox"]'));
  const materiaAllChecked = materiaCheckboxes.length > 0 && materiaCheckboxes.every(cb => cb.checked);
  const materiaSomeChecked = materiaCheckboxes.some(cb => cb.checked);
  
  const selectAllMateria = document.querySelector('.select-all-materia input[type="checkbox"]');
  if (selectAllMateria) {
    selectAllMateria.checked = materiaAllChecked;
    selectAllMateria.indeterminate = !materiaAllChecked && materiaSomeChecked;
  }

  // Verificar grupo de Profesor
  const profesorCheckboxes = Array.from(document.querySelectorAll('.columns-container-profesor input[type="checkbox"]'));
  const profesorAllChecked = profesorCheckboxes.length > 0 && profesorCheckboxes.every(cb => cb.checked);
  const profesorSomeChecked = profesorCheckboxes.some(cb => cb.checked);
  
  const selectAllProfesor = document.querySelector('.select-all-profesor input[type="checkbox"]');
  if (selectAllProfesor) {
    selectAllProfesor.checked = profesorAllChecked;
    selectAllProfesor.indeterminate = !profesorAllChecked && profesorSomeChecked;
  }
}

// Actualizar el estado del checkbox principal
function updateMainCheckbox() {
  const allCheckboxes = Array.from(document.querySelectorAll('.columns-container-materia input[type="checkbox"], .columns-container-profesor input[type="checkbox"]'));
  const allChecked = allCheckboxes.length > 0 && allCheckboxes.every(cb => cb.checked);
  const someChecked = allCheckboxes.some(cb => cb.checked);
  
  const mainCheckbox = document.querySelector('.select-first input[type="checkbox"]');
  if (mainCheckbox) {
    mainCheckbox.checked = allChecked;
    mainCheckbox.indeterminate = !allChecked && someChecked;
  }
}

function descargarExcelSeleccionado() {
  // Obtener todas las columnas seleccionadas de ambos contenedores
  var columnasSeleccionadas = Array.from(
    document.querySelectorAll(
      '.columns-container-materia input[type="checkbox"]:checked, .columns-container-profesor input[type="checkbox"]:checked'
    )
  ).map((checkbox) => checkbox.value);

  if (columnasSeleccionadas.length === 0) {
    alert("Por favor, selecciona al menos una columna.");
    return;
  }

  const departamento_id = document.getElementById("departamento_id").value;
  const url = `./functions/basesdedatos/descargar-data-excel.php?departamento_id=${departamento_id}&columnas=${encodeURIComponent(JSON.stringify(columnasSeleccionadas))}`;

  window.location.href = url;
  cerrarDescargarExcel();
}

function descargarExcelCotejado() {
  const departamento_id = document.getElementById("departamento_id").value;
  const url = `./functions/basesdedatos/descargar-cotejo.php?departamento_id=${departamento_id}`;
  window.location.href = url;
  cerrarDescargarExcel();
}

// Cerrar el modal al hacer clic en la X
document.querySelector('.close').onclick = function() {
  cerrarDescargarExcel();
}

// Cerrar el modal al hacer clic fuera de él
window.onclick = function(event) {
  if (event.target == document.getElementById('modal-descargar')) {
      cerrarDescargarExcel();
  }
}

// Inicializar los handlers cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  initializeCheckboxHandlers();
});